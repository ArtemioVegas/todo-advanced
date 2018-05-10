<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180502101713 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_2FB3D0EEA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, user_id, created_at, updated_at, project_name FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, user_id INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, project_name VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project (id, user_id, created_at, updated_at, project_name) SELECT id, user_id, created_at, updated_at, project_name FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEA76ED395 ON project (user_id)');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('DROP INDEX IDX_527EDB25A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, user_id, project_id, task_name, upload_original_name, upload_file, created_at, updated_at, due_date, complete_date, is_done, description FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER NOT NULL, user_id INTEGER NOT NULL, project_id INTEGER NOT NULL, task_name VARCHAR(255) NOT NULL COLLATE BINARY, upload_original_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, upload_file VARCHAR(255) DEFAULT NULL COLLATE BINARY, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, due_date DATE NOT NULL, complete_date DATETIME DEFAULT NULL, is_done BOOLEAN DEFAULT \'0\' NOT NULL, description CLOB NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, user_id, project_id, task_name, upload_original_name, upload_file, created_at, updated_at, due_date, complete_date, is_done, description) SELECT id, user_id, project_id, task_name, upload_original_name, upload_file, created_at, updated_at, due_date, complete_date, is_done, description FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25A76ED395 ON task (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_2FB3D0EEA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, user_id, created_at, updated_at, project_name FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, user_id INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, project_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO project (id, user_id, created_at, updated_at, project_name) SELECT id, user_id, created_at, updated_at, project_name FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEA76ED395 ON project (user_id)');
        $this->addSql('DROP INDEX IDX_527EDB25A76ED395');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, user_id, project_id, task_name, description, upload_original_name, upload_file, created_at, updated_at, due_date, complete_date, is_done FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER NOT NULL, user_id INTEGER NOT NULL, project_id INTEGER NOT NULL, task_name VARCHAR(255) NOT NULL, upload_original_name VARCHAR(255) DEFAULT NULL, upload_file VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, due_date DATE NOT NULL, complete_date DATETIME DEFAULT NULL, is_done BOOLEAN DEFAULT \'0\' NOT NULL, description CLOB DEFAULT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO task (id, user_id, project_id, task_name, description, upload_original_name, upload_file, created_at, updated_at, due_date, complete_date, is_done) SELECT id, user_id, project_id, task_name, description, upload_original_name, upload_file, created_at, updated_at, due_date, complete_date, is_done FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25A76ED395 ON task (user_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
    }
}
