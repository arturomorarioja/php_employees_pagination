        <h2>Departments</h2>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th><a href="index.php?v=d&s=name" class="header">Name</a></th>
                    <th><a href="index.php?v=d&s=manager" class="header">Manager</a></th>
                </tr>
            </thead>
            <tbody>
            <?php
                require_once 'model/department.php';

                $sort = trim($_GET['s'] ?? '');

                $departments = new Department;
                foreach ($departments->list($sort) as $department) {
            ?>
                <tr>
                    <td><?=$department['dept_name'] ?></td>
                    <td><?=$department['manager'] ?></td>
                </tr>        
            <?php } ?>
            </tbody>
        </table>