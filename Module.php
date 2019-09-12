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
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec('CREATE TABLE datascribe_record (id INT AUTO_INCREMENT NOT NULL, datascribe_item_id INT NOT NULL, transcriber_id INT DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_56286518995C6F7 (datascribe_item_id), INDEX IDX_56286518D962F77 (transcriber_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
        $conn->exec('CREATE TABLE datascribe_item (id INT AUTO_INCREMENT NOT NULL, datascribe_project_id INT NOT NULL, omeka_item_id INT NOT NULL, completed_by_id INT DEFAULT NULL, approved_by_id INT DEFAULT NULL, completed DATETIME DEFAULT NULL, approved DATETIME DEFAULT NULL, synced DATETIME DEFAULT NULL, INDEX IDX_4B14C13288812DF4 (datascribe_project_id), INDEX IDX_4B14C132D093185F (omeka_item_id), INDEX IDX_4B14C13285ECDE76 (completed_by_id), INDEX IDX_4B14C1322D234F6A (approved_by_id), UNIQUE INDEX UNIQ_4B14C13288812DF4D093185F (datascribe_project_id, omeka_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
        $conn->exec('CREATE TABLE datascribe_field (id INT AUTO_INCREMENT NOT NULL, datascribe_project_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, position INT NOT NULL, data_type VARCHAR(255) NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_6979265F88812DF4 (datascribe_project_id), UNIQUE INDEX UNIQ_6979265F88812DF4462CE4F5 (datascribe_project_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
        $conn->exec('CREATE TABLE datascribe_project (id INT AUTO_INCREMENT NOT NULL, item_set_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, guidelines LONGTEXT DEFAULT NULL, record_label VARCHAR(255) NOT NULL, synced DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_B4494447960278D7 (item_set_id), INDEX IDX_B44944477E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
        $conn->exec('ALTER TABLE datascribe_record ADD CONSTRAINT FK_56286518995C6F7 FOREIGN KEY (datascribe_item_id) REFERENCES datascribe_item (id) ON DELETE CASCADE;');
        $conn->exec('ALTER TABLE datascribe_record ADD CONSTRAINT FK_56286518D962F77 FOREIGN KEY (transcriber_id) REFERENCES user (id) ON DELETE SET NULL;');
        $conn->exec('ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C13288812DF4 FOREIGN KEY (datascribe_project_id) REFERENCES datascribe_project (id) ON DELETE CASCADE;');
        $conn->exec('ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C132D093185F FOREIGN KEY (omeka_item_id) REFERENCES item (id) ON DELETE CASCADE;');
        $conn->exec('ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C13285ECDE76 FOREIGN KEY (completed_by_id) REFERENCES user (id) ON DELETE SET NULL;');
        $conn->exec('ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C1322D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id) ON DELETE SET NULL;');
        $conn->exec('ALTER TABLE datascribe_field ADD CONSTRAINT FK_6979265F88812DF4 FOREIGN KEY (datascribe_project_id) REFERENCES datascribe_project (id) ON DELETE CASCADE;');
        $conn->exec('ALTER TABLE datascribe_project ADD CONSTRAINT FK_B4494447960278D7 FOREIGN KEY (item_set_id) REFERENCES item_set (id) ON DELETE SET NULL;');
        $conn->exec('ALTER TABLE datascribe_project ADD CONSTRAINT FK_B44944477E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL;');
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_record;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_item;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_field;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_project;');
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
    }
}
