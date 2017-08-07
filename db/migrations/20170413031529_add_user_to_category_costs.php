<?php

use Phinx\Migration\AbstractMigration;

class AddUserToCategoryCosts extends AbstractMigration
{
    /** adicionar usuario para categoria de custo  */
    public function up()
    {
        $this->table('category_costs')
            ->addColumn('user_id','integer')
            ->addForeignKey('user_id','users','id')
            ->save();
    }
    /** remove a chave , depois a coluna desfazendo a relação */
    public function down()
    {
        $this->table('category_costs')
            ->dropForeignKey('user_id')
            ->removeColumn('user_id');
    }
}
