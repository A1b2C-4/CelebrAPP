<?php
require 'config/database.php';
include 'includes/header.php';

$sql = "SELECT * FROM birthdays ORDER BY fecha_nacimiento ASC";
$result = $conn->query($sql);
?>
<h2>Lista de cumpleaños</h2>
<table>
    <tr>
        <th>Nombre completo</th>
        <th>Fecha de nacimiento</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Relación</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
        <td><?= date('d/m/Y', strtotime($row['fecha_nacimiento'])) ?></td>
        <td><?= htmlspecialchars($row['telefono']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['tipo_relacion']) ?></td>
        <td class="actions">
    </br>
            <a href="edit_birthday.php?id=<?= $row['id'] ?>" class="button">Editar</a>
            <span style="display:inline-block; width:10px;"></span>
            <a href="delete_birthday.php?id=<?= $row['id'] ?>" class="button" onclick="return confirm('¿Seguro que deseas eliminar este cumpleaños?');">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php
include 'includes/footer.php';
?> 