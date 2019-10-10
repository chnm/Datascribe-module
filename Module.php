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

        // Set the corresponding visibility rules on DataScribe resources.
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $filter = $em->getFilters()->getFilter('resource_visibility');
        $filter->addRelatedEntity('Datascribe\Entity\DataScribeItem', 'item_id');
    }

    public function install(ServiceLocatorInterface $services)
    {
        $sql = <<<'SQL'
CREATE TABLE datascribe_user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, project_id INT UNSIGNED NOT NULL, user_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_D99C3265166D1F9C (project_id), INDEX IDX_D99C3265A76ED395 (user_id), UNIQUE INDEX UNIQ_D99C3265166D1F9CA76ED395 (project_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE datascribe_value (id INT UNSIGNED AUTO_INCREMENT NOT NULL, field_id INT UNSIGNED NOT NULL, record_id INT UNSIGNED NOT NULL, is_invalid TINYINT(1) DEFAULT NULL, is_missing TINYINT(1) DEFAULT NULL, is_illegible TINYINT(1) DEFAULT NULL, needs_review TINYINT(1) DEFAULT NULL, data LONGTEXT NOT NULL COMMENT '(DC2Type:json)', INDEX IDX_2FFB3B33443707B0 (field_id), INDEX IDX_2FFB3B334DFD750C (record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE datascribe_record (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED NOT NULL, owner_id INT DEFAULT NULL, approved_by_id INT DEFAULT NULL, created DATETIME NOT NULL, is_approved TINYINT(1) DEFAULT NULL, approved DATETIME DEFAULT NULL, INDEX IDX_5628651126F525E (item_id), INDEX IDX_56286517E3C61F9 (owner_id), INDEX IDX_56286512D234F6A (approved_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE datascribe_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, dataset_id INT UNSIGNED NOT NULL, item_id INT NOT NULL, prioritized_by_id INT DEFAULT NULL, locked_by_id INT DEFAULT NULL, completed_by_id INT DEFAULT NULL, synced_by_id INT DEFAULT NULL, approved_by_id INT DEFAULT NULL, prioritized DATETIME DEFAULT NULL, locked DATETIME DEFAULT NULL, completed DATETIME DEFAULT NULL, synced DATETIME DEFAULT NULL, is_approved TINYINT(1) DEFAULT NULL, approved DATETIME DEFAULT NULL, INDEX IDX_4B14C132D47C2D1B (dataset_id), INDEX IDX_4B14C132126F525E (item_id), INDEX IDX_4B14C1324A0B323 (prioritized_by_id), INDEX IDX_4B14C1327A88E00 (locked_by_id), INDEX IDX_4B14C13285ECDE76 (completed_by_id), INDEX IDX_4B14C1327141DBAD (synced_by_id), INDEX IDX_4B14C1322D234F6A (approved_by_id), UNIQUE INDEX UNIQ_4B14C132D47C2D1B126F525E (dataset_id, item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE datascribe_field (id INT UNSIGNED AUTO_INCREMENT NOT NULL, dataset_id INT UNSIGNED NOT NULL, `label` VARCHAR(255) NOT NULL, hint LONGTEXT DEFAULT NULL, position INT NOT NULL, is_primary TINYINT(1) DEFAULT '0' NOT NULL, data_type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, data LONGTEXT NOT NULL COMMENT '(DC2Type:json)', UNIQUE INDEX UNIQ_6979265F5E237E06 (name), INDEX IDX_6979265FD47C2D1B (dataset_id), UNIQUE INDEX UNIQ_6979265FD47C2D1B462CE4F5 (dataset_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE datascribe_dataset (id INT UNSIGNED AUTO_INCREMENT NOT NULL, project_id INT UNSIGNED NOT NULL, item_set_id INT DEFAULT NULL, synced_by_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, guidelines LONGTEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, synced DATETIME DEFAULT NULL, created DATETIME NOT NULL, is_public TINYINT(1) DEFAULT '0' NOT NULL, UNIQUE INDEX UNIQ_2C5AD5795E237E06 (name), INDEX IDX_2C5AD579166D1F9C (project_id), INDEX IDX_2C5AD579960278D7 (item_set_id), INDEX IDX_2C5AD5797141DBAD (synced_by_id), INDEX IDX_2C5AD5797E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE datascribe_project (id INT UNSIGNED AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created DATETIME NOT NULL, is_public TINYINT(1) DEFAULT '0' NOT NULL, UNIQUE INDEX UNIQ_B44944475E237E06 (name), INDEX IDX_B44944477E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
ALTER TABLE datascribe_user ADD CONSTRAINT FK_D99C3265166D1F9C FOREIGN KEY (project_id) REFERENCES datascribe_project (id) ON DELETE CASCADE;
ALTER TABLE datascribe_user ADD CONSTRAINT FK_D99C3265A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_value ADD CONSTRAINT FK_2FFB3B33443707B0 FOREIGN KEY (field_id) REFERENCES datascribe_field (id) ON DELETE CASCADE;
ALTER TABLE datascribe_value ADD CONSTRAINT FK_2FFB3B334DFD750C FOREIGN KEY (record_id) REFERENCES datascribe_record (id) ON DELETE CASCADE;
ALTER TABLE datascribe_record ADD CONSTRAINT FK_5628651126F525E FOREIGN KEY (item_id) REFERENCES datascribe_item (id) ON DELETE CASCADE;
ALTER TABLE datascribe_record ADD CONSTRAINT FK_56286517E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_record ADD CONSTRAINT FK_56286512D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C132D47C2D1B FOREIGN KEY (dataset_id) REFERENCES datascribe_dataset (id) ON DELETE CASCADE;
ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C132126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE;
ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C1324A0B323 FOREIGN KEY (prioritized_by_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C1327A88E00 FOREIGN KEY (locked_by_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C13285ECDE76 FOREIGN KEY (completed_by_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C1327141DBAD FOREIGN KEY (synced_by_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_item ADD CONSTRAINT FK_4B14C1322D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_field ADD CONSTRAINT FK_6979265FD47C2D1B FOREIGN KEY (dataset_id) REFERENCES datascribe_dataset (id) ON DELETE CASCADE;
ALTER TABLE datascribe_dataset ADD CONSTRAINT FK_2C5AD579166D1F9C FOREIGN KEY (project_id) REFERENCES datascribe_project (id) ON DELETE CASCADE;
ALTER TABLE datascribe_dataset ADD CONSTRAINT FK_2C5AD579960278D7 FOREIGN KEY (item_set_id) REFERENCES item_set (id) ON DELETE SET NULL;
ALTER TABLE datascribe_dataset ADD CONSTRAINT FK_2C5AD5797141DBAD FOREIGN KEY (synced_by_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_dataset ADD CONSTRAINT FK_2C5AD5797E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE datascribe_project ADD CONSTRAINT FK_B44944477E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL;
SQL;
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec($sql);
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_user;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_value;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_record;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_item;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_field;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_dataset;');
        $conn->exec('DROP TABLE IF EXISTS datascribe_project;');
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
    }
}
