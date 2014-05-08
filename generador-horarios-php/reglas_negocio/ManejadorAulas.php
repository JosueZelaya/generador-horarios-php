<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorAulas
 *
 * @author arch
 */
class ManejadorAulas {
    //put your code here
    
    public static function getTodasAulas(){
        $aulas = array();
        $sql_consulta = "SELECT * FROM aulas ORDER BY capacidad ASC";
	$respuesta = conexion::consulta2($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $aula = new Aula();
            $aula->setNombre($fila['nombre']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setDisponible($fila['disponible']);
            $aulas[] = $aula;
        }
        return $aulas;
    }
    
    public static function getTodasAulasOrdenadas($ordenarPor){
        $aulas = array();
        $sql_consulta = "SELECT * FROM aulas ORDER BY ".$ordenarPor." DESC";
	$respuesta = conexion::consulta2($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $aula = new Aula();
            $aula->setNombre($fila['nombre']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setDisponible($fila['disponible']);
            $aulas[] = $aula;
        }
        return $aulas;
    }
    
    public static function elegirAulaDiferente($aulas,$aulasUsadas){
        //Si ya se usaron todas las aulas entonces no seguimos buscando y devolvemos null
        if(count($aulas) ==  count($aulasUsadas)){
            return null;
        }
        $aula = ManejadorAulas::elegirAula($aulas);
        for ($index = 0; $index < count($aulasUsadas); $index++) {
            if($aula->getNombre()==$aulasUsadas[$index]->getNombre()){
                $aula = ManejadorAulas::elegirAulaDiferente($aulas, $aulasUsadas);
            }
        }
        return $aulas;
    }
    
    public static function elegirAula($aulas){
        $desde=0;
        $hasta = count($aulas)-1;
        $aula = Procesador::getNumeroAleatorio($desde, $hasta);
        return $aulas[$aula];
    }
    
    public static function obtenerAulasPorCapacidad($aulas,$num_alumnos){
        $aulasSeleccionadas = array();
        for ($index = 0; $index < count($aulas); $index++) {
            $aula = $aulas[$index];
            $capacidad = $aula->getCapacidad();
            if($capacidad >= $num_alumnos){
                $aulasSeleccionadas[] = $aulas[$index];
            }
        }
        return $aulasSeleccionadas;
    }

    /**
     * Devuelve el horario de la semana para un aula específica
     * 
     * @param type $aulas = Las aulas
     * @param type $aula = El nombre del aula
     * @param type $materias = Las materias
     */
    public static function getHorarioEnAula($aulas,$aula,$materias){
        for ($index = 0; $index < count($aulas); $index++) {
            if($aulas[$index]->getNombre()==$aula){
                
            }
        }
    }
    
//    public static DefaultTableModel getHorarioEnAula(ArrayList<Aula> aulas, String aula, DefaultTableModel table, ArrayList<Materia> materias){
//        for(int i=0; i<aulas.size(); i++){
//            if(aulas.get(i).getNombre().equals(aula)){
//                ArrayList<Dia> dias = aulas.get(i).getDias();
//                for(int x=0; x<dias.size(); x++){
//                    ArrayList<Hora> horas = dias.get(x).getHoras();
//                    for(int y=0; y<horas.size(); y++){
//                        Hora hora = horas.get(y);
//                        Grupo grupo = hora.getGrupo();
//                        if(!hora.estaDisponible() && grupo.getId_Agrup() != 0){
//                            String propietario = obtenerNombrePropietario(grupo.getId_Agrup(),materias);
//                            String texto = propietario+" GT: "+grupo.getId_grupo();
//                            table.setValueAt(texto, y, x+1);
//                        }else
//                            table.setValueAt("", y, x+1);
//                    }
//                }
//                break;
//            }
//        }
//        return table;
//    }
//    
//    public static DefaultTableModel getHorarioEnAula_Depar(ArrayList<Aula> aulas, String aula, DefaultTableModel table, int id_depar, ArrayList<Agrupacion> agrups, ArrayList<Materia> materias){
//        for(int i=0; i<aulas.size(); i++){
//            if(aulas.get(i).getNombre().equals(aula)){
//                ArrayList<Dia> dias = aulas.get(i).getDias();
//                for(int x=0; x<dias.size(); x++){
//                    ArrayList<Hora> horas = dias.get(x).getHoras();
//                    for(int y=0; y<horas.size(); y++){
//                        Hora hora = horas.get(y);
//                        Grupo grupo = hora.getGrupo();
//                        if(obtenerIdDepartamento(grupo.getId_Agrup(), agrups) == id_depar){
//                            String texto = obtenerNombrePropietario(grupo.getId_Agrup(),materias)+" GT: "+grupo.getId_grupo();
//                            table.setValueAt(texto, y, x+1);
//                        }else
//                            table.setValueAt("", y, x+1);
//                    }
//                }
//                break;
//            }
//        }
//        
//        return table;
//    }
//    
//    public static DefaultTableModel getHorarioEnAula_Carrera(ArrayList<Aula> aulas, String aula, DefaultTableModel table, ArrayList ids_agrups, ArrayList<Materia> materias){
//        
//        for(int i=0; i<aulas.size(); i++){
//            if(aulas.get(i).getNombre().equals(aula)){
//                ArrayList<Dia> dias = aulas.get(i).getDias();
//                for(int x=0; x<dias.size(); x++){
//                    ArrayList<Hora> horas = dias.get(x).getHoras();
//                    for(int y=0; y<horas.size(); y++){
//                        Grupo grupo = horas.get(y).getGrupo();
//                        for(int z=0; z<ids_agrups.size(); z++){
//                            if((int)ids_agrups.get(z) == grupo.getId_Agrup()){
//                                table.setValueAt(obtenerNombrePropietario(grupo.getId_Agrup(),materias)+" GT: "+grupo.getId_grupo(), y, x+1);
//                                break;
//                            }
//                            else
//                                table.setValueAt("", y, x+1);
//                        }
//                    }
//                }
//                break;
//            }
//        }
//        return table;
//    }
    
    
}
