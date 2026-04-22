// utils/PlagiatChecker.php
class PlagiatChecker {
    private $apiKey = 'votre_cle_api';
    private $apiUrl = 'https://api.plagiarism.com/check';

    public function check($filepath) {
        // Exemple avec cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new CURLFile($filepath),
            'api_key' => $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            return json_decode($response, true);
        }
        return null;
    }
}