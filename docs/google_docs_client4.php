<?php
require_once '../vendor/autoload.php';  // Path autoload ของ Composer

// ฟังก์ชันสำหรับสร้าง Client ของ Google API
function getGoogleClient()
{
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/../credentials/nithes-844fcf19df5b.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->addScope(Google_Service_Docs::DOCUMENTS);
    $client->setIncludeGrantedScopes(true);
    $client->setSubject('nithes@nithes.iam.gserviceaccount.com'); // อีเมลของ Service Account
    return $client;
}

// ฟังก์ชันสำหรับสร้าง Google Docs จาก Template
function createGoogleDoc($data)
{
    $client = getGoogleClient();

    $drive = new Google_Service_Drive($client);
    $docs = new Google_Service_Docs($client);

    $templateId = '1koKmKUqvh858lqy6FO_iW8MTf0oq-eWp4wu_Z5Vn6_U'; // ✅ Template ใน Shared Drive
    $folderId = '0AE9lpdWZYUvaUk9PVA'; // ✅ Folder ID ของ Shared Drive

    try {
        // ✅ คัดลอก Template ไปยังโฟลเดอร์ใน Shared Drive
        $copy = new Google_Service_Drive_DriveFile([
            'name' => 'กราฟภาพรวมของแต่ละสาขา',
            'parents' => [$folderId]
        ]);

        $copied = $drive->files->copy(
            $templateId,
            $copy,
            ['supportsAllDrives' => true]
        );

        $docId = $copied->getId();

        $imageUrl = 'https://nc.ac.th/note/image/chart/chart_2.png';

        // ✅ สร้างคำสั่งแทนที่ข้อความ
        $requests = [
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
                    'containsText' => [
                        'text' => '{{status_users}}',
                        'matchCase' => true
                    ],
                    'replaceText' => $data['status_users']
                ]
            ]),
            new Google_Service_Docs_Request([
                'replaceAllText' => [
                    'containsText' => [
                        'text' => '{{img}}',
                        'matchCase' => true
                    ],
                    'replaceText' => ''
                ]
            ]),
            // แทรกรูปภาพในตำแหน่งที่ต้องการ
            new Google_Service_Docs_Request([
                'insertInlineImage' => [
                    'location' => ['index' => 1],
                    'uri' => $imageUrl,
                    'objectSize' => [
                        'height' => ['magnitude' => 300, 'unit' => 'PT'],
                        'width' => ['magnitude' => 500, 'unit' => 'PT'],
                    ]
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