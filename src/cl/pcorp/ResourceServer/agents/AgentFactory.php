<?php
namespace cl\pcorp\ResourceServer\agents;

use cl\pcorp\ResourceServer\common\Factory;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class AgentFactory extends Factory {

  /**
   * @param string $agentName
   * @param string $resourceType
   * @throws FileNotFoundException
   * @return ifAgent
   */
  public function getAgent(string $agentName, string $resourceType) {
    $this->setBasename($this->getAgentBasename($agentName, $resourceType));
    $this->setNamespace(__NAMESPACE__ . "\\" . $resourceType);
    $this->setBasePath(__DIR__ . '/' . $resourceType . '/');
    return $this->getInstance();
  }

  private function getAgentBasename(string $agentName, string $resourceType) {
    return ucfirst($agentName)
      . ucfirst($resourceType)
      . "Agent";

  }
}