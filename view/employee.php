        <h2>Employees</h2>
    </header>
    <main>
        <?php
            $searchText = trim($_GET['search'] ?? '');
            $urlSearch = '';
            if ($searchText !== '') {
                $urlSearch = "&search=$searchText";
            }
        ?>
        <form action="index.php" method="GET">
            <fieldset>
                <label for="txtSearch">Name</label>
                <input type="text" id="txtSearch" name="search" value="<?=$searchText ?>" required>
                <input type="hidden" name="v" value="e">
                <button type="submit">Search</button>
            </fieldset>
        </form>
        <table>
            <thead>
                <tr>
                    <th><a href="index.php?v=e<?=$urlSearch ?>&s=last_name" class="header">Last Name</a></th>
                    <th><a href="index.php?v=e<?=$urlSearch ?>&s=first_name" class="header">First Name</a></th>                    
                    <th><a href="index.php?v=e<?=$urlSearch ?>&s=department" class="header">Department</a></th>                    
                    <th><a href="index.php?v=e<?=$urlSearch ?>&s=gender" class="header">Gender</a></th>                    
                    <th><a href="index.php?v=e<?=$urlSearch ?>&s=birth_date" class="header">Birth Date</a></th>                    
                    <th><a href="index.php?v=e<?=$urlSearch ?>&s=hire_date" class="header">Hire Date</a></th>                    
                    <th><a href="index.php?v=e<?=$urlSearch ?>&s=salary" class="header number">Salary</a></th>                    
                </tr>
            </thead>
            <tbody>
            <?php
                require_once 'model/employee.php';

                $range = (int) htmlspecialchars($_GET['r'] ?? 1);
                $total = 0;

                $sort = htmlspecialchars(trim($_GET['s'] ?? ''));

                $searchText = htmlspecialchars(trim($_GET['search'] ?? ''));

                $employees = new Employee;
                $totalEmployees = $employees->total($searchText);
                $numPages = ceil($totalEmployees / Employee::NUM_ROWS);
                if ($range > $numPages) {
                    $range = $numPages;
                }
                if ($searchText === '') {
                    $employeeList = $employees->list($range, $sort);
                } else {
                    $employeeList = $employees->search($searchText, $range, $sort);
                }
                foreach ($employeeList as $employee) {
                    $total++;
            ?>
                <tr>
                    <td><?=htmlspecialchars($employee['last_name']) ?></td>
                    <td><?=htmlspecialchars($employee['first_name']) ?></td>
                    <td><?=htmlspecialchars($employee['dept_name']) ?></td>
                    <td><?=htmlspecialchars($employee['gender']) ?></td>
                    <td><?=htmlspecialchars($employee['birth_date']) ?></td>
                    <td><?=htmlspecialchars($employee['hire_date']) ?></td>
                    <td class="number"><?=htmlspecialchars($employee['salary']) ?></td>
                </tr>        
            <?php 
                } 
                $firstEmployee = (($range - 1) * Employee::NUM_ROWS) + 1;
                $lastEmployee = $firstEmployee + $total - 1;
            ?>
            </tbody>
        </table>
        <nav id="pagination">
            <?php $queryParams = ($searchText !== '' ? "&search=$searchText" : '') . ($sort !== '' ? "&s=$sort" : ''); ?>
            <!-- Data navigation -->
            <a href="index.php?v=e&r=1<?=$queryParams ?>">1</a>
            <?php if ($range > 1): ?>
                <a href="index.php?v=e&r=<?=($range - 1) ?><?=$queryParams ?>">
                    &lt;&lt;
                </a>
            <?php endif; ?>
            <?php if ($range > 1): ?>
                &nbsp;<?=$range ?>&nbsp;
            <?php endif; ?>
            <?php if ($total === Employee::NUM_ROWS): ?>
                <a href="index.php?v=e&r=<?=($range + 1) ?><?=$queryParams ?>">
                    &gt;&gt;
                </a>
            <?php endif; ?>
            <a href="index.php?v=e&r=<?=$numPages . $queryParams ?>"><?=number_format((float) $numPages) ?></a>

            <?php if ($lastEmployee === 0): ?>
                &nbsp;No employees to show
            <?php else: ?>
                &nbsp;Showing employees <?=$firstEmployee ?> to <?=$lastEmployee ?> of <?=number_format((float) $totalEmployees) ?>
            <?php endif; ?>
        </nav> 