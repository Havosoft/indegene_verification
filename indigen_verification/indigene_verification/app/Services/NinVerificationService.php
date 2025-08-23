<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NinVerificationService
{
    /**
     * Verify NIN using the external API
     *
     * @param string $nin
     * @param \App\Models\User $user
     * @return array
     */
    public function verifyNin($nin, $user = null)
    {
        try {
            $response = Http::timeout(30)->get('https://nimc.collaboratoor.com/get_nin_details.php', [
                'nin' => $nin
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('NIN verification response', [
                    'nin' => $nin,
                    'response' => $data
                ]);

                if (isset($data['status']) && $data['status'] === 'success') {
                    $ninData = $data['data'];
                    
                    // If user is provided, perform comprehensive validation
                    if ($user) {
                        $validationResult = $this->validateUserAgainstNin($user, $ninData);
                        
                        if ($validationResult['is_valid']) {
                            return [
                                'status' => 'success',
                                'data' => $ninData,
                                'message' => 'NIN verification successful'
                            ];
                        } else {
                            return [
                                'status' => 'failed',
                                'data' => $ninData,
                                'validation_details' => $validationResult,
                                'message' => 'NIN verification failed - validation errors'
                            ];
                        }
                    } else {
                        // Basic validation for backward compatibility
                        $isTarabaIndigene = $this->isTarabaIndigene($ninData);
                        
                        return [
                            'status' => $isTarabaIndigene ? 'success' : 'failed',
                            'data' => $ninData,
                            'message' => $isTarabaIndigene ? 'NIN verification successful' : 'Not a Taraba indigene'
                        ];
                    }
                } else {
                    return [
                        'status' => 'failed',
                        'message' => $data['message'] ?? 'NIN verification failed'
                    ];
                }
            } else {
                return [
                    'status' => 'failed',
                    'message' => 'NIN verification service is currently unavailable. Please try again later.'
                ];
            }
        } catch (\Exception $e) {
            Log::error('NIN verification error', [
                'nin' => $nin,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => 'NIN verification failed. Please try again later.'
            ];
        }
    }

    /**
     * Comprehensive validation of user data against NIN data
     *
     * @param \App\Models\User $user
     * @param array $ninData
     * @return array
     */
    private function validateUserAgainstNin($user, $ninData)
    {
        $validationErrors = [];
        $isValid = true;

        // 1. Check State of Origin (must be Taraba)
        $ninState = strtolower(trim($ninData['state_of_origin'] ?? ''));
        if ($ninState === '' || $ninState === 'unknown') {
            $validationErrors[] = 'state_unknown';
            $isValid = false;
        } elseif ($ninState !== 'taraba') {
            $validationErrors[] = 'not_taraba_state';
            $isValid = false;
        }

        // 2. Check LGA match (registration LGA must match NIN LGA)
        $userLga = strtolower(trim($user->lga ?? ''));
        $ninLga = strtolower(trim($ninData['lga_of_origin'] ?? ''));
        
        if ($ninLga === '' || $ninLga === 'unknown') {
            $validationErrors[] = 'lga_unknown';
            $isValid = false;
        } elseif ($userLga !== $ninLga) {
            $validationErrors[] = 'lga_mismatch';
            $isValid = false;
        }

        // 3. Check Date of Birth match
        $userDob = $user->date_of_birth;
        $ninDob = $ninData['dob'] ?? '';
        
        if ($ninDob === '' || $ninDob === 'unknown') {
            $validationErrors[] = 'dob_unknown';
            $isValid = false;
        } elseif ($userDob !== $ninDob) {
            $validationErrors[] = 'dob_mismatch';
            $isValid = false;
        }

        return [
            'is_valid' => $isValid,
            'errors' => $validationErrors,
            'nin_state' => $ninData['state_of_origin'] ?? '',
            'nin_lga' => $ninData['lga_of_origin'] ?? '',
            'nin_dob' => $ninData['dob'] ?? '',
            'user_lga' => $user->lga ?? '',
            'user_dob' => $user->date_of_birth ?? ''
        ];
    }

    /**
     * Check if the person is a Taraba indigene (basic check)
     *
     * @param array $ninData
     * @return bool
     */
    private function isTarabaIndigene($ninData)
    {
        $stateOfOrigin = strtolower($ninData['state_of_origin'] ?? '');
        return $stateOfOrigin === 'taraba';
    }

    /**
     * Get verification failure reason
     *
     * @param array $ninData
     * @param array $validationDetails
     * @return string
     */
    public function getVerificationFailureReason($ninData, $validationDetails = null)
    {
        // If we have detailed validation results, use them
        if ($validationDetails && isset($validationDetails['errors'])) {
            return $this->getDetailedFailureReason($validationDetails);
        }

        // Fallback to basic validation for backward compatibility
        $state = trim($ninData['state_of_origin'] ?? '');
        $lga   = trim($ninData['lga_of_origin'] ?? ($ninData['lga'] ?? ''));

        // If we can't determine state, guide the user to update their NIN profile
        if ($state === '' || strtolower($state) === 'unknown') {
            return "We could not determine your State of Origin from your NIN record. Please visit any NIMC enrollment center to update your profile and try again. If you believe this is a mistake, contact support for assistance.";
        }

        // If state is not Taraba, explain eligibility
        if (strtolower($state) !== 'taraba') {
            $location = $state . ($lga ? " — {$lga} LGA" : '');
            return "Your NIN record shows your State of Origin as {$location}, not Taraba. Only Taraba State indigenes are eligible for verification on this platform. If your NIN details are incorrect, please update them at any NIMC center and try again, or contact support for help.";
        }

        // Fallback (in case other rule fails)
        return "Your NIN verification could not be completed at this time. Please try again later or contact support.";
    }

    /**
     * Get detailed failure reason based on validation errors
     *
     * @param array $validationDetails
     * @return string
     */
    private function getDetailedFailureReason($validationDetails)
    {
        $errors = $validationDetails['errors'];
        $messages = [];

        foreach ($errors as $error) {
            switch ($error) {
                case 'state_unknown':
                    $messages[] = "• Your State of Origin could not be determined from your NIN record";
                    break;
                case 'not_taraba_state':
                    $state = $validationDetails['nin_state'];
                    $lga = $validationDetails['nin_lga'];
                    $location = $state . ($lga ? " — {$lga} LGA" : '');
                    $messages[] = "• Your NIN shows State of Origin as {$location}, not Taraba State";
                    break;
                case 'lga_unknown':
                    $messages[] = "• Your LGA of Origin could not be determined from your NIN record";
                    break;
                case 'lga_mismatch':
                    $userLga = $validationDetails['user_lga'];
                    $ninLga = $validationDetails['nin_lga'];
                    $messages[] = "• LGA mismatch: You registered as {$userLga} but your NIN shows {$ninLga}";
                    break;
                case 'dob_unknown':
                    $messages[] = "• Your Date of Birth could not be determined from your NIN record";
                    break;
                case 'dob_mismatch':
                    $userDob = $validationDetails['user_dob'];
                    $ninDob = $validationDetails['nin_dob'];
                    $messages[] = "• Date of Birth mismatch: You registered with {$userDob} but your NIN shows {$ninDob}";
                    break;
            }
        }

        $failureMessage = "Your NIN verification failed due to the following issues:\n\n" . implode("\n", $messages);
        
        $failureMessage .= "\n\n**What you can do:**\n";
        $failureMessage .= "• If your NIN details are incorrect, visit any NIMC enrollment center to update them\n";
        $failureMessage .= "• If you believe this is an error, contact our support team\n";
        $failureMessage .= "• Only Taraba State indigenes with matching registration details are eligible for verification";

        return $failureMessage;
    }
}