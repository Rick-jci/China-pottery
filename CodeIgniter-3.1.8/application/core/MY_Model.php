<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class MY_Model extends CI_Model implements Base_Model{
	
	protected $table = '';
	
	//配置端
    public function __construct() {
        //过滤传入
        $this->check_array($_GET);
        $this->check_array($_POST);
        
        parent::__construct();
        
        //连接数据库
        $this -> db = $this -> load -> database("default", true);
    }
	
	protected function setTableName($tableName=''){
		 $this->table = $this -> db -> dbprefix($tableName);
	}
	
	/**
	 * 写入数据到数据库
	 * @return id 若为0，则插入失败，1 则成功
	 * */
	public function insert($array=null){
		$id = 0;
		if($array != null){
			$id = $this->db->insert($this->table,$array);
		}
		return $id;
	}
	
	/**
	 * 根据id更新数据内容
	 * @return $flag true 则添加成功，false 添加失败
	 * */
	public function update($array=null,$whereArr=null){
		$flag = false;
		if($array!=null && $whereArr!=null){
			$query = $this->db->update($this->table,$array,$whereArr);
			if($query){
				$flag = true;
			}
		}
		return $flag;
	}
	
	/**
	 * 删除数据
	 * @return $flag true 删除成功，false删除失败
	 * */
	public function delete($id=0,$key=null){
		$flag = false;
		if($id>0 && !empty($key)){
			$where = "{$key}={$id}";
			$sql = "DELETE FROM {$this->table} WHERE {$where}";
			$query = $this->db->query($sql);
			if($query){
				$flag = true;
			}
		}
		return $flag;
	}
	
	/**
	 * 查找表内的内容
	 * $what 查什么 id title content
	 * $where 条件 id=1 category=2
	 * $order 排序
	 * $limit 条数
	 * */
	public function select($what='*',$where='1=1',$order='1=1',$limit=null){
		$sql = '';
		if($limit){
			$sql = "SELECT {$what} FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";
		}else{
			$sql = "SELECT {$what} FROM {$this->table} WHERE {$where} ORDER BY {$order}";
		}
		$array = $this->db->query($sql)->result_array();
		return $array;
	}
	
	/**
	 * 查询并返回一条数据
	 * $whereArr:(array)查询的条件
	 * $type:返回结果类型，默认为obj格式，arr为数组格式
	 * return:查询结果
	 */
	public function row($whereArr,$orderby = "") {
		$this->db->where ( $whereArr );
		if (! empty ( $orderby )) {
			$orderby = str_replace ( "@", " ", $orderby );
			$this->db->order_by ( $orderby );
		}
		$query = $this->db->get ( $this->table );
		return $query->row_array ();
	}
	
	/**
	 * 查询并返回多条数据
	 * $whereArr:(array)查询的条件
	 * $num:单页显示的条数
	 * $page:当前页数
	 * $orderby:排序条件
	 * return:查询结果
	 */
	public function result($whereArr, $page = 1, $num = 10, $orderby = "") {
		if ($page == 0) $page = 1;
		$offset = ($page - 1) * $num;
		$this->db->where ( $whereArr );
		if (! empty ( $orderby )) {
			$orderby = str_replace ( "@", " ", $orderby );
			$this->db->order_by ( $orderby );
		}
		$query = $this->db->get ( $this->table, $num, $offset );
		return $query->result_array ();
	}
	
	/**
	 * sql注入检测
	 * */
	//查找组
	private function check_array($array=null){
		if($array){
			foreach($array as $v){
				$this->verify_id($v);
			}
		}
		return $array;
	}
	//Manage inject_check
	private function verify_id($id=null) {
		//如果为array，调用check_array 类似递归思想~
		if(is_array($id)){
			$this->check_array($id);
		}elseif($this->inject_check($id)) {
            exit('提交的参数非法！');
        }
        //$id = intval($id);
        return $id;
    }
    
    //正则检测
	private function inject_check($sql_str) {
		//正则表达式匹配 数据库操纵
		$flag = preg_match('/\s+select\s+|\s+insert\s+|\s+and\s+|\s+or\s+|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i', $sql_str.'');
        //if($flag){ echo $sql_str.'<BR/>';print_r($arr);}
        return $flag;
    }
	
}

// 对表的基本操作(CURD操作)
interface Base_Model {
	//插入
	function insert($array=null);
	//更新
	function update($id=0,$array=null);
	//删除
	function delete($id=0,$key=null);
	//查找
	function select($what='*',$where='1=1',$order='1=1');
}
