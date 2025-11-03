<?php
require_once '../vendor/autoload.php';  // Path autoload ของ Composer

// ฟังก์ชันสำหรับสร้าง Client ของ Google API
function getGoogleClient() {
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/../credentials/nithes-844fcf19df5b.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->addScope(Google_Service_Docs::DOCUMENTS);
    $client->setIncludeGrantedScopes(true);
    $client->setSubject('nithes@nithes.iam.gserviceaccount.com'); // อีเมลของ Service Account
    return $client;
}

// ฟังก์ชันสำหรับสร้าง Google Docs จาก Template
function createGoogleDoc($data) {
    $client = getGoogleClient();

    $drive = new Google_Service_Drive($client);
    $docs  = new Google_Service_Docs($client);

    $templateId = '1V4jF669vYOxohHgcfohTunX8U2R8OIH3LiZYDOGoz9Q'; // ✅ Template ใน Shared Drive
    $folderId   = '0AE9lpdWZYUvaUk9PVA'; // ✅ Folder ID ของ Shared Drive

    try {
        // ✅ คัดลอก Template ไปยังโฟลเดอร์ใน Shared Drive
        $copy = new Google_Service_Drive_DriveFile([
            'name' => 'รายงานสรุปบันทึกการเรียนการสอนแต่ละสาขา',
            'parents' => [$folderId]
        ]);

        $copied = $drive->files->copy(
            $templateId,
            $copy,
            ['supportsAllDrives' => true]
        );

        $docId = $copied->getId();

        // ✅ สร้างคำสั่งแทนที่ข้อความ
        $requests = [
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{day}}', 'matchCase' => true],
                    'replaceText' => strval($data['day'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{month}}', 'matchCase' => true],
                    'replaceText' => strval($data['month'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{year}}', 'matchCase' => true],
                    'replaceText' => strval($data['year'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{termSelect}}', 'matchCase' => true],
                    'replaceText' => strval($data['termSelect'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{week}}', 'matchCase' => true],
                    'replaceText' => strval($data['week'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{branchName}}', 'matchCase' => true],
                    'replaceText' => strval($data['branchName'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{sent}}', 'matchCase' => true],
                    'replaceText' => strval($data['sent'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{notSent}}', 'matchCase' => true],
                    'replaceText' => strval($data['notSent'])
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => ['text' => '{{teacherName}}', 'matchCase' => true],
                    'replaceText' => strval($data['teacherName'])
                ]
            ])
        ];

        $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        // ✅ อัปเดตเอกสารที่คัดลอกมา
        $docs->documents->batchUpdate($docId, $batchUpdateRequest);

        return $docId;
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
        return null;
    }
}
?>
