<?php

require_once 'connection.php';

/**
 * Employee class
 * 
 * @author Arturo Mora-Rioja
 * @version 1.0.0 August 2020
 * @version 1.0.1 December 2024 Adapted to PHP8's syntax
 */
class Employee 
{
    public const NUM_ROWS = 20;

    /**
     * Retrieves the total number of employees
     * 
     * @param @searchText Filter by employee first or last name
     * @return the number of employees in the database,
     *      or a string with an error message if there is an error
     */
    public function total(string $searchText = ''): int|string
    {
        $db = new DB();
        $con = $db->connect();
        if ($con) {
            $query = <<<'SQL'
                SELECT COUNT(*) AS total
                FROM employees
                    LEFT JOIN dept_emp ON employees.emp_no = dept_emp.emp_no 
                    LEFT JOIN salaries ON employees.emp_no = salaries.emp_no
                WHERE dept_emp.from_date <= NOW() 
                  AND dept_emp.to_date > NOW()
                  AND salaries.from_date <= NOW() 
                  AND salaries.to_date > NOW()
            SQL;
            $searchText = trim($searchText);
            if ($searchText !== '') {
                $query .= <<<'SQL'
                    AND (
                        employees.last_name LIKE ? 
                        OR employees.first_name LIKE ?
                    )
                SQL;
            }
            $query .= ';';

            try {
                $stmt = $con->prepare($query);
                if ($searchText !== '') {
                    $stmt->execute(['%' . $searchText . '%', '%' . $searchText . '%']);
                } else {
                    $stmt->execute();
                }
                $db->disconnect($con);

                if ($stmt->rowCount() === 1) {
                    return $stmt->fetch()['total'];
                }
                return DB::ERROR;
            } catch (PDOException $e) {
                return DB::ERROR;
            }
        } else {
            return DB::ERROR;
        }
    }

    /**
     * Retrieves information of employees
     * 
     * @param   $range the range number of rows to return, in groups of 25
     * @param   $sort  field by which to sort the retrieved information. None if an empty string
     * @return  an associative array with employee information,
     *      or a string with an error message if there is an error
     */
    public function list(int $range, string $sort): array|string
    {
        $db = new DB();
        $con = $db->connect();
        if ($con) {
            $offset = ($range - 1) * self::NUM_ROWS;
            $query = <<<'SQL'
                SELECT employees.emp_no, employees.last_name, employees.first_name, 
                    departments.dept_name, employees.gender, 
                    DATE_FORMAT(employees.birth_date, "%d/%m/%Y") as birth_date, 
                    DATE_FORMAT(employees.hire_date, "%d/%m/%Y") as hire_date, salaries.salary
                FROM employees 
                    LEFT JOIN dept_emp ON employees.emp_no = dept_emp.emp_no 
                    LEFT JOIN departments ON dept_emp.dept_no = departments.dept_no
                    LEFT JOIN salaries ON employees.emp_no = salaries.emp_no
                WHERE dept_emp.from_date <= NOW() 
                  AND dept_emp.to_date > NOW()
                  AND salaries.from_date <= NOW() 
                  AND salaries.to_date > NOW()
            SQL;
            
            switch ($sort) {
                case 'last_name':
                    $query .= 'ORDER BY employees.last_name';
                    break;
                case 'first_name':
                    $query .= 'ORDER BY employees.first_name';
                    break;
                case 'department':
                    $query .= 'ORDER BY departments.dept_name';
                    break;
                case 'gender':
                    $query .= 'ORDER BY employees.gender';
                    break;
                case 'birth_date':
                    $query .= 'ORDER BY employees.birth_date';
                    break;
                case 'hire_date':
                    $query .= 'ORDER BY employees.hire_date';
                    break;
                case 'salary':
                    $query .= 'ORDER BY salaries.salary';
                    break;
            }
            $query .= ' LIMIT ' . self::NUM_ROWS . " OFFSET $offset;";
    
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

    /**
     * Retrieves the employees whose first or last name includes a certain text
     * 
     * @param   $searchText text upon which to execute the search
     * @param   $range      the range number of rows to return, in groups of 25
     * @param   $sort       field by which to sort the retrieved information. None if an empty string
     * @return  an associative array with employee information,
     *      or a string with an error message if there is an error
     */
    public function search(string $searchText, int $range, string $sort = ''): array|string
    {
        $db = new DB();
        $con = $db->connect();
        if ($con) {
            $offset = ($range - 1) * self::NUM_ROWS;
            $query = <<<'SQL'
                SELECT employees.emp_no, employees.last_name, 
                    employees.first_name, departments.dept_name, employees.gender, 
                    DATE_FORMAT(employees.birth_date, "%d/%m/%Y") as birth_date, 
                    DATE_FORMAT(employees.hire_date, "%d/%m/%Y") as hire_date, salaries.salary
                FROM employees 
                    LEFT JOIN dept_emp ON employees.emp_no = dept_emp.emp_no 
                    LEFT JOIN departments ON dept_emp.dept_no = departments.dept_no
                    LEFT JOIN salaries ON employees.emp_no = salaries.emp_no
                WHERE dept_emp.from_date <= NOW() 
                  AND dept_emp.to_date > NOW()
                  AND salaries.from_date <= NOW() 
                  AND salaries.to_date > NOW()
                  AND (
                    employees.last_name LIKE ? 
                    OR employees.first_name LIKE ?
                  )
            SQL;

            switch ($sort) {
                case 'last_name':
                    $query .= 'ORDER BY employees.last_name';
                    break;
                case 'first_name':
                    $query .= 'ORDER BY employees.first_name';
                    break;
                case 'department':
                    $query .= 'ORDER BY departments.dept_name';
                    break;
                case 'gender':
                    $query .= 'ORDER BY employees.gender';
                    break;
                case 'birth_date':
                    $query .= 'ORDER BY employees.birth_date';
                    break;
                case 'hire_date':
                    $query .= 'ORDER BY employees.hire_date';
                    break;
                case 'salary':
                    $query .= 'ORDER BY salaries.salary';
                    break;
            }

            $query .= ' LIMIT ' . self::NUM_ROWS . " OFFSET $offset;";

            try {
                $stmt = $con->prepare($query);
                $stmt->execute(['%' . $searchText . '%', '%' . $searchText . '%']);                
                $db->disconnect($con);
                
                return $stmt->fetchAll();                
            } catch(PDOException $e) {
                return DB::ERROR;
            }
        } else {
            return DB::ERROR;
        }
    }
}