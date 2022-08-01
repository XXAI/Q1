<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'Usuario Root',
            'username' => 'root',
            'password' => Hash::make('ssa.plataforma'),
            'email' => 'root@localhost',
            'is_superuser' => 1,
            'avatar' => '/assets/avatars/50-king.svg'
        ]);

        $lista_csv = [
            'permissions',
            'roles',
            'role_user',
            'permission_role',
            'catalogo_entidades_federativas',
            'catalogo_distritos',
            'catalogo_municipios',
            'catalogo_localidades',
            'catalogo_tipos_accidentes',
            'catalogo_tipos_vehiculos',
            'catalogo_causantes',
            'catalogo_causas',
            'catalogo_condiciones_caminos',
            'catalogo_condiciones_naturales',
            'catalogo_tipos_fallas_vehiculos',
            'catalogo_tipos_zonas',
            'catalogo_estados_vias',
            'catalogo_tipos_pavimentos'
            

        ];

        foreach($lista_csv as $csv){

            $archivo_csv = storage_path().'/app/seeds/'.$csv.'.csv';

            $query = sprintf("
                LOAD DATA local INFILE '%s' 
                INTO TABLE $csv 
                CHARACTER SET utf8
                FIELDS TERMINATED BY ',' 
                OPTIONALLY ENCLOSED BY '\"' 
                ESCAPED BY '\"' 
                LINES TERMINATED BY '\\n' 
                IGNORE 1 LINES", addslashes($archivo_csv)
            );
            echo $query;
            DB::connection()->getpdo()->exec($query);

        }


    }
}
