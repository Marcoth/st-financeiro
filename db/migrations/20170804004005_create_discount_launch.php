<?php

use Phinx\Migration\AbstractMigration;

class CreateDiscountLaunch extends AbstractMigration
{

    public function up()
    {
        $this->table('discount_launches')
            ->addColumn('date_discount_launch','date')
            ->addColumn('type','string')
            ->addColumn('value','float')
            ->addColumn('created_at','datetime')
            ->addColumn('updated_at','datetime')
            ->addColumn('bill_pay_id','integer')
            ->addForeignKey('bill_pay_id', 'bill_pays', 'id')
            ->save();

    }

    public function down()
    {
        $this->dropDatabase('discount_launches');
    }
}
