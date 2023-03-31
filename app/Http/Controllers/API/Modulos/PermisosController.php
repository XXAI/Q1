<?php

namespace App\Http\Controllers\API\Modulos;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use \Validator,\Hash, \Response, \DB, \File, \Store;
use App\Exports\DevReportExport;

use App\Http\Requests;
use App\Models\User;

class PermisosController extends Controller
{
    public function lesiones(Request $request)
    {
        $loggedUser = auth()->userOrFail();

        try{
            $access = $this->getUserAccessData();
            
            $permisos = User::with('roles.permissions','permissions')->find($loggedUser->id);

            $permisos_especiales = [];
            if(!$access->is_admin){
                foreach ($permisos->roles as $key => $value) {
                    
                    foreach ($value->permissions as $key2 => $value2) {
                        if($value2->id == 'EV7n1jFHymsUVBAGa1Bo6XSvMZfg64kh')
                        {
                            $permisos_especiales[] = "permisoGuardarIncidente";
                        }
                        
                     
                    }
                }
                    
                foreach ($permisos->permissions as $key2 => $value2) {
                    if($value2->id == 'EV7n1jFHymsUVBAGa1Bo6XSvMZfg64kh')
                    {
                        $permisos_especiales[] = "permisoGuardarIncidente";
                    }
                    
                   
                }
                
            }else if($access->is_admin){
                $permisos_especiales[] = "permisoAdmin";
            }
            return response()->json(['data'=>$permisos_especiales],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    private function getUserAccessData($loggedUser = null){
        if(!$loggedUser){
            $loggedUser = auth()->userOrFail();
        }

        $accessData = (object)[];

        if (\Gate::allows('has-permission', \Permissions::ADMIN_PERSONAL_ACTIVO)){
            $accessData->is_admin = true;
        }else{
            $accessData->is_admin = false;
        }

        return $accessData;
    }
}
