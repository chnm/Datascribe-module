<?php
namespace Datascribe;

use Omeka\Module\AbstractModule;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include sprintf('%s/config/module.config.php', __DIR__);
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);
    }

    public function install(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0');
        $conn->exec('CREATE TABLE datascribe_project (id INT AUTO_INCREMENT NOT NULL, item_set_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, guidelines LONGTEXT DEFAULT NULL, record_label VARCHAR(255) NOT NULL, synced DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_B4494447960278D7 (item_set_id), INDEX IDX_B44944477E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
        $conn->exec('ALTER TABLE datascribe_project ADD CONSTRAINT FK_B4494447960278D7 FOREIGN KEY (item_set_id) REFERENCES item_set (id) ON DELETE SET NULL;');
        $conn->exec('ALTER TABLE datascribe_project ADD CONSTRAINT FK_B44944477E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL;');
        $conn->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0');
        $conn->exec('DROP TABLE IF EXISTS datascribe_project');
        $conn->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
    }
}
