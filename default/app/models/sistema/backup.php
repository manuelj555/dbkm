<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Modelo para el manejo de las copias de seguridad
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Backup extends ActiveRecord {
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {       
        $this->belongs_to('usuario');        
    }
                    
    /**
     * Método para buscar usuarios
     */
    public function getAjaxBackup($field, $value, $order='', $page=0) {
        $value = Filter::get($value, 'string');
        if( strlen($value) <= 2 OR ($value=='none') ) {
            return NULL;
        }
        $columns = 'backup.*, persona.nombre, persona.apellido';        
        $join = 'INNER JOIN usuario ON usuario.id = backup.usuario_id ';
        $join.= 'INNER JOIN persona ON persona.id = usuario.persona_id ';
        $conditions = "backup.id > 0";
        
        $order = $this->get_order($order, 'nombre', array('nombre'      =>array(
                                                                                'ASC'=>'persona.nombre ASC, persona.apellido ASC', 
                                                                                'DESC'=>'persona.nombre DESC, persona.apellido DESC'),
                                                          'apellido'    =>array('ASC'=>'persona.apellido ASC, persona.nombre ASC', 
                                                                                'DESC'=>'persona.apellido DESC, persona.nombre DESC')
                                                          ));
        //Por seguridad defino los campos habilitados para la búsqueda
        $fields = array('denominacion', 'nombre', 'apellido', 'fecha');
        if(!in_array($field, $fields)) {
            $field = 'nombre';
        }        
        
        if($field=='fecha') {
            $conditions.= " AND DATE(backup.registrado_at) LIKE '%$value%'";
        } else {  
            $conditions.= " AND $field LIKE '%$value%'";
        }        
        
        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        }  
    }
    
    /**
     * Método para listar las copias de seguridad
     * @param type $order patrón de ordenamiento order.campo.tipo
     * @param type $page
     * @return type
     */
    public function getListadoBackup($order='', $page=0) {
        $columns = 'backup.*, persona.nombre, persona.apellido';        
        $join = 'INNER JOIN usuario ON usuario.id = backup.usuario_id ';
        $join.= 'INNER JOIN persona ON persona.id = usuario.persona_id ';                        
        
        $order = $this->get_order($order, 'nombre', array('nombre'      =>array(
                                                                                'ASC'=>'persona.nombre ASC, persona.apellido ASC', 
                                                                                'DESC'=>'persona.nombre DESC, persona.apellido DESC'),
                                                          'apellido'    =>array('ASC'=>'persona.apellido ASC, persona.nombre ASC', 
                                                                                'DESC'=>'persona.apellido DESC, persona.nombre DESC')
                                                          ));                
        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "order: $order");
        }  
    }
    
    
    /**
     * Método para crear una copia de seguridad
     * 
     * @param type $data Input del post
     * @param string $path Ruta donde se almacenará la copia de seguridad
     * @param type $database Pull de conexión
     * @return boolean|\Backup
     */
    public static function createBackup($data, $path='', $database='') {
        $obj = new Backup($data);
        $obj->archivo = "backup-".($obj->count() + 1 ).".sql.gz";
        $obj->usuario_id = Session::get('id');
        //Inicio transacción
        ActiveRecord::beginTrans();
        if(!$obj->create()) {            
            ActiveRecord::rollbackTrans();
            return FALSE;
        }
        if(empty($path)) {
            $path = dirname(APP_PATH) . '/public/files/backup/';
        }
        if(!is_writable($path)) {
            ActiveRecord::rollbackTrans();
            DwMessage::error('Error: BKP-CRE001. El directorio de las copias de seguridad no tiene permisos de escritura.');
            return false;
        }        
        $file = $path.$obj->archivo;
        $system = $obj->_getSystem(); 
        $database = (empty($databases)) ? Config::get('config.application.database') : $database;
        $config = $obj->_getConfig($database);        
        $exec = "$system -h ".$config['host']." -u ".$config['username']." --password=".$config['password']." --opt --default-character-set=latin1 ".$config['name']." | gzip > $file";
        system($exec, $resultado);
        if($resultado) {
            ActiveRecord::rollbackTrans();
            DwMessage::error('Error: BKP-CRE002. Se ha producido un error al intentar crear una nueva copia de seguridad.');
            return false;            
        }        
        $tamano = filesize($file);                
        $clase = array(" Bytes", " KB", " MB", " GB", " TB"); 
        $obj->tamano =  round($tamano/pow(1024,($i = floor(log($tamano, 1024)))), 2 ).$clase[$i];
        $obj->update();
        @chmod($file, 0744);        
        ActiveRecord::commitTrans();        
        return $obj;         
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     * @return string
     */
    public function before_save() {
        $conditions = "denominacion = '$this->denominacion'";
        $conditions.= (isset($this->id)) ? " AND id != $this->id" : '';
        if($this->count($conditions)) {
            DwMessage::error('Lo sentimos, pero ya existe una copia de seguridad con la misma denominación.');
            return 'cancel';
        }
    }
       
    
}
?>