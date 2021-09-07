<?php
namespace App;
class dbcreate {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function createtables() {
        $sql = ['CREATE TABLE IF NOT EXISTS projects (
        			project_id INTEGER PRIMARY KEY,
        			project_name TEXT NOT NULL
        			)',
        		'CREATE TABLE IF NOT EXISTS tasks (
        			task_id INTEGER PRIMARY KEY,
        			task_name  VARCHAR (255) NOT NULL,
        			completed  INTEGER NOT NULL,
        			start_date TEXT,
        			completed_date TEXT,
        			project_id VARCHAR (255),
        			FOREIGN KEY (project_id)
        			REFERENCES projects(project_id) ON UPDATE CASCADE ON DELETE CASCADE
        			)'];
        foreach ($sql as $sqli) {
            $this->pdo->exec($sqli);
        }
    }
    public function insertdata($tblname,$fields) {
    	if ($tblname == 'projects') {
    		$sql = 'INSERT INTO '.$tblname.'(project_name) VALUES(:project_name)';
    		$stmt = $this->pdo->prepare($sql);
    		$stmt->bindValue(':project_name',$fields['project_name']);
    		$stmt->execute();
    		return $this->pdo->lastInsertId();
    	}
    	if ($tblname == 'tasks') {
    		$sql = 'INSERT INTO '.$tblname.
    				' (task_name,start_date,completed_date,completed,project_id) 
    				VALUES(:task_name,:start_date,:completed_date,:completed,:project_id)';
    		$stmt = $this->pdo->prepare($sql);
    		$stmt->execute([':task_name' => $fields['task_name'],
    						':start_date' => $fields['start_date'],
    						':completed_date' => $fields['completed_date'],
    						':completed' => $fields['completed'],
    						':project_id' => $fields['project_id']
    						]);
    		return $this->pdo->lastInsertId();
    	}
    }
    public function getdata($tblname,$project_id,$task_id) {
    	if($project_id==null && $task_id==null) {
    		$stmt = $this->pdo->query('SELECT * FROM '.$tblname);
    	}
    	elseif ($tblname=='tasks' && $task_id!=null) {
    		$stmt = $this->pdo->query('SELECT * FROM '.$tblname.' WHERE task_id=:task_id');
    		$stmt->execute([':task_id' => $task_id]);
    	}
    	elseif ($task_id==null) {
    		$stmt = $this->pdo->query('SELECT * FROM '.$tblname.' WHERE project_id=:project_id');
    		$stmt->execute([':project_id' => $project_id]);
    	}
    	$datalist = [];
    	while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    		if ($tblname=='projects')
    		$datalist[] = [':project_id' => $row['project_id'],
    					  ':project_name' => $row['project_name']
    					 ];
    		else
    		$datalist[] = [':task_id' => $row['task_id'],
    					  ':task_name' => $row['task_name'],
    					  ':start_date' => $row['start_date'],
    					  ':completed_date' => $row['completed_date'],
    					  ':completed' => $row['completed'],
    					  ':project_id' => $row['project_id']
    					 ];
    	}
    	return $datalist;
    }
    public function deletedata($tblname,$id) {
    	switch ($tblname) {
    		case 'tasks': 
    			$sql = 'DELETE FROM '.$tblname.' WHERE task_id=:task_id';
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindValue(':task_id',$id);
				$stmt->execute();
				//return $stmt->rowCount();
				break;
    		case 'projects':
    			$sql = 'DELETE FROM tasks WHERE project_id=:project_id';
    			$stmt = $this->pdo->prepare($sql);
    			$stmt->execute([':project_id' => $id]);
    			$sql2 = 'DELETE FROM '.$tblname.' WHERE project_id=:project_id';
    			$stmt = $this->pdo->prepare($sql2);
    			$stmt->execute([':project_id' => $id]);
    	}
    }
    public function updatedata($tblname, $fields) {
   		$field = array_keys($fields);
   		if ($tblname=='projects') {
   			$sql = "UPDATE ".$tblname." SET ".$field[1]."=:".$field[1]." WHERE ".$field[0]."=:".$field[0];
   			$stmt = $this->pdo->prepare($sql);
   			$stmt->bindValue(':'.$field[1],$fields[$field[1]]);
   			$stmt->bindValue(':'.$field[0],$fields[$field[0]]);
   			return $stmt->execute();
   		}
   		elseif ($tblname=='tasks') {
   			$sql = "UPDATE ".$tblname." SET ".$field[1]."=:".$field[1].","
   											 .$field[2]."=:".$field[2].","
   											 .$field[3]."=:".$field[3].","
   											 .$field[4]."=:".$field[4].","
   											 .$field[5]."=:".$field[5].
   									" WHERE ".$field[0]."=:".$field[0];
   			$stmt = $this->pdo->prepare($sql);
   			$stmt->bindValue(':'.$field[1],$fields[$field[1]]);
   			$stmt->bindValue(':'.$field[2],$fields[$field[2]]);
   			$stmt->bindValue(':'.$field[3],$fields[$field[3]]);
   			$stmt->bindValue(':'.$field[4],$fields[$field[4]]);
   			$stmt->bindValue(':'.$field[5],$fields[$field[5]]);
   			$stmt->bindValue(':'.$field[0],$fields[$field[0]]);
   			return $stmt->execute();
   		}
    }
}
?>