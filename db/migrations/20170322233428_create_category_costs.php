<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryCosts extends AbstractMigration
{
    /** criar/alterar migração/tabelas no banco */
    public function up()
    {
        $this->table('category_costs')
            ->addColumn('name', 'string')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->save();
    }

    /** reverter uma migração*/
    public function down()
    {
        $this->dropTable('category_costs');
    }
}
