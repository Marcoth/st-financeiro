<?php

use Phinx\Seed\AbstractSeed;
use STFin\Models\BillPay;

/** Seed para lançamento de descontos */
class DiscountLaunchSeeder extends AbstractSeed
{
    /** definição dos tipos de descontos  */
    const TIPOS = [
        'Antecipação de pagamento',
        'Indicação',
        'Fidelidade',
    ];
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $billPays;
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        require __DIR__ . '/../bootstrap.php';
        /** @var  billPays - instancia do collection do pacote eloquete do orm que consulta todas as contas a pagar */
        $this->billPays = BillPay::all();
        /** acessso aos dados falsos*/
        $faker = \Faker\Factory::create('pt_BR');
        /** adiciona provedores para gerar dados falsos */
        $faker->addProvider($this);
        $discountLaunch = $this->table('discount_launches');
        $data = [];
        foreach (range(1, 20) as $value) {
            $userId = rand(1,4);
            $data[] = [
                'date_discount_launch' => $faker->dateTimeBetween('-1 month')->format('Y-m-d'),
                'type' => $faker->discountLaunchType(),
                'value' => $faker->randomFloat(2,10,1000),
                'bill_pay_id' => $faker->billPayId($userId),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        $discountLaunch->insert($data)->save();
    }
    /** provider para pegar a conta a pagar  pelo usuário*/
    public function billPayId($userId){
        /** @var  $billPays - where permite fazer uma busca na coleção */
        $billPays = $this->billPays->where('user_id',$userId);
        /** @var  $billPays - pega todos os models de contas a pagar referente ao usuario
        e transforma em uma coleção de ids
         */
        $billPays = $billPays->pluck('id');
        /**  basicamente, disponibiliza esse metodo criado para o faker */
        return \Faker\Provider\Base::randomElement($billPays->toArray());
    }
    /** gera elemento randomicamente de tipos de desconto - A constante de Tipos */
    public function discountLaunchType(){
        return \Faker\Provider\Base::randomElement(self::TIPOS);
    }
}
