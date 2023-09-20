<?

	class Departments extends model{
		var $table = "departments";

		public static $deptClass = null;

		function __construct()
		{
			self::$deptClass = $this;
		}

		function search($name){
			$sql = "select name, id from departments where status = 'active' and name like '%".$name."%'";
			return fetchRows($sql);
		}
		function searchResults($name="") {
			$sql = "select * from departments where status = 'active' and name like '%".$name."%' order by name";
			// echo $sql;die();
			return fetchRows($sql);
		}
}
