<?php
/**
 * @namspace Erc\Api\RequiredDocuments
 * @name ClientRequiredDocuments
 * Summary: #$END$#
 *
 * Date: 2023-02-01
 * Time: 4:34 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\ClassReader\fixtures;

use Erc\Api\QueryRunner;
use League\Container\Container;
use PDO;

class ClientRequiredDocuments
{
    protected ?Container $container = null;
    protected ?int $clientId = null;
    protected ?array $requiredDocs = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function getContainer():Container {
        return $this->container;
    }

    public function setClientId(int $clientId) {
        $this->clientId = $clientId;
    }

    public function getClientId():int {
        return $this->clientId;
    }
    public function getRequiredDocuments(): array {
        $sql = <<<EOF
SELECT rdp.document,
       rdp.document_status,
       rdp.migrated,
       rd.document_name
FROM required_document_progress rdp
JOIN required_documents rd on rd.id = rdp.document
WHERE rdp.client = :client_id AND document_status = 0;
EOF;
        $values = [
            'client_id' => $this->clientId
        ];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        $this->requiredDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->requiredDocs;
    }

    public function documentsComplete(): bool {
        if(is_null($this->requiredDocs)) $this->getRequiredDocuments();
        $totalDocs = 0;
        foreach($this->requiredDocs as $doc) {
            $totalDocs += $doc['document_status'];
        }
        return (count($this->requiredDocs) == $totalDocs);
    }

    /**
     * Look to see if we have all the documents to complete an associated, related task (such as payroll).
     * @param string $task
     * @return bool
     * @todo In order to enable, you neeed to change the getRequiredDocuemnts() query to include rd.related_tasks and the associated tests.
     */
//    public function documentsCompleteFor(string $task): bool {
//        $taskCount = 0;
//        $taskDocs = 0;
//        foreach($this->requiredDocs as $doc) {
//            if($doc['related_task'] != $task) continue;
//            $taskCount++;
//            $taskDocs += $doc['document_status'];
//        }
//
//        return ($taskDocs == $taskCount);
//    }

}
