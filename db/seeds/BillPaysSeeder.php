<?php

use Phinx\Seed\AbstractSeed;

class BillPaysSeeder extends AbstractSeed
{

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $categories;
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
        /** @var  \STFin\Application  - instancia do application
         * para obter os servico de autenticacao e acessar o hash do password
         */
        require __DIR__ . '/../bootstrap.php';
        /** @var  categories - instancia do collection do pacote eloquete do orm que consulta todas as categorias */
        $this->categories = \STFin\Models\CategoryCost::all();
        /** acessso aos dados falsos*/
        $faker = \Faker\Factory::create('pt_BR');
        /** adiciona provedores para gerar dados falsos */
        $faker->addProvider($this);
        $billPays = $this->table('bill_pays');
        $data = [];
        foreach (range(1, 20) as $value) {
            $userId = rand(1,4);
            $recurrent = rand(0,1);
            $data[] = [
                'date_launch' => $faker->dateTimeBetween('-1 month')->format('Y-m-d'),
                'name' => $faker->word,
                'recurrent' => (string)$recurrent,
                'value' => $faker->randomFloat(2,10,1000),
                'user_id' => $userId,
                'category_cost_id' => $faker->categoryId($userId),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        $billPays->insert($data)->save();
    }
    /** provider para pegar a categoria pelo usuário*/
    public function categoryId($userId){
        /** @var  $categories - where permite fazer uma busca na coleção */
        $categories = $this->categories->where('user_id',$userId);
        /** @var  $categories - pega todos os models de categories referente ao usuario
        e transforma em uma coleção de ids
         */
        $categories = $categories->pluck('id');
        /**  basicamente, disponibiliza esse metodo criado para o faker */
        return \Faker\Provider\Base::randomElement($categories->toArray());
    }
}
