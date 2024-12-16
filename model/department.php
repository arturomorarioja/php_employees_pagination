<?php

 require_once 'connection.php';

/**
 * Department class
 * 
 * @author Arturo Mora-Rioja
 * @version 1.0.0 August 2020
 * @version 1.0.1 December 2024 Adapted to PHP8's syntax
 */

class Department 
{
    /**
     * Retrieves information of departments
     * 
     * @param   field by which to sort the retrieved information. None if an empty string
     * @return  an array with department information,
     *      or a string with an error message if there is an error
     */
    function list(string $sort = ''): array|string
    {
        $db = new DB;
        $con = $db->connect();
        if ($con) {
            $query = <<<'SQL'
                SELECT departments.dept_no, departments.dept_name, 
                    CONCAT(employees.last_name, ', ', employees.first_name) AS manager
                FROM departments 
                    LEFT JOIN dept_manager ON departments.dept_no = dept_manager.dept_no
                    LEFT JOIN employees ON dept_manager.emp_no = employees.emp_no
                WHERE dept_manager.from_date <= NOW() 
                  AND dept_manager.to_date > NOW() 
            SQL;

            switch ($sort) {
                case 'name':
                    $query .= ' ORDER BY departments.dept_name;';
                    break;  
                case 'manager':
                    $query .= ' ORDER BY manager;';
                    break;
                default:
                    $query .= ';';
            }

            try {
                $stmt = $con->query($query);                
                $db->disconnect($con);
                
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                return DB::ERROR;
            }
        } else {
            return DB::ERROR;
        }
    }
}