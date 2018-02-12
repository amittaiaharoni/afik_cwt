<?
    class mail_factory extends base_factory{
        //////////////////////////////////////////////////////////////////////////
        //	BASIC FUNCTIONALITY
        const db_table_name = "mail_template";
        const instance_class_name = "mail";
        const cont_class_name = "mail_controller";

        protected static $factory = null;
        protected static $instances;

        protected function __construct() {
            parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
        }
        /////////////////////////////////////////////////////////////////////////
        //	CUSTOM FUNCTIONS
        protected function extend_instance(&$instance){

        }

        protected function new_object_created(&$new_object){

        }

        public function dt_custom_search($columns){

        }

        public function get_active_mails()
        {
            return $this->data->{$this->class_name}->get_by_column("active",1);
        }
        /////////////////////////////////////////////////////////////////////////
    }


    //////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////


    class mail extends base_data_object{

        protected function __construct($data, $fields_info, $db_table_name) {
            parent::__construct($data, $fields_info, $db_table_name);
        }

        protected function extend_deletion(){

        }

        public static function sort_by_example($a, $b){
            return $a->something > $b->something ? 1 : -1;
        }

    }


    //////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////


    class mail_view extends view_abstract{

        public function __construct($file_path) {
            global $is_admin;

            $this->url_page_name = ($is_admin?"admin_":"") . "mail_template";
            $this->object_class_name = "mail";

            parent::__construct($file_path);
        }

        public function post_render_html(&$html){

        }
    }


    //////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////


    class mail_controller extends controller_abstract{

        public function __construct() {
            global $is_admin;

            $this->url_page_name = ($is_admin?"admin_":"") . "mail_template";
            $this->object_class_name = "mail";
            $this->view_class_name = "mail_view";

            parent::__construct();

            // db name - dt name - object path as array - formatter function
            $this->dt_columns = array(
                array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
                array( 'db' => 'name_trans_id', 'dt' => 'name',			'path' => "name"),
                array( 'db' => 'text_trans_id', 'dt' => 'text',			'path' => "text"),
                array( 'db' => 'link',          'dt' => 'link',			'path' => "link")

                // array(
                // 'db'        => 'start_date',
                // 'dt'        => 4,
                // 'formatter' => function( $d, $row ) {
                // return date( 'jS M y', strtotime($d));
                // }
                // )
            );
        }

        public function extend_request_processing(){

        }

        public function admin_insert_custom_data($new_object){

        }

        public function admin_edit_custom_data(){

			/*if (!empty($_REQUEST['update_id'])){
				$obj = $this->data->{$this->object_class_name}->get_by_id((int)$_REQUEST['update_id']);
				if (!empty($obj)){
                    if (!empty($_POST['text'])){

                      $string = $obj->text;
                      //$birthday =  $this->data->user->get_by_id()->birthday;

                      $replacement_arr = array('birthdate' => date("d-m-Y"), 'cart' => 'My cart html');

                      foreach($replacement_arr AS $key => $value)
                      {
                          $string = str_replace('[['.$key.']]', $value, $string);
                      }

                      $obj->text = $string;

                    }

                    $obj->save();
                }
                unset($obj);
            }*/
        }

        protected function send_waiting_mails($tmp_id)
        {
            // 1. find out which template used and which placeholders
            // 2.1. check date for user/cart created_date/birthday and
            // 2.2. if after today and less of end of month
            // 3. send change place holders to user/cart data
            // 4. send mail to account.
        }
    }

?>
