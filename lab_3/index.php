<?php

$day = (int)date('N');

function johnSchedule(int $day) {
  if ($day === 1 || $day === 3 || $day === 5) {
    return "08:00 - 12:00";
  }

  return "Нерабочий день";
}

function janeSchedule(int $day) {
  if ($day === 2 || $day === 4 || $day === 6) {
    return "12:00 - 16:00";
  }

  return "Нерабочий день";
}


$john = johnSchedule($day);
$jane = janeSchedule($day);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Schedule</title>
  <style>
    table { border-collapse: collapse; width: 520px; }
    th, td { border: 1px solid #999; padding: 8px; text-align: left; }
  </style>
</head>
<body>
  <h2>Расписание на сегодня (<?= date('d.m.Y') ?>)</h2>

  <table>
    <tr>
      <th>№</th>
      <th>Фамилия Имя</th>
      <th>График работы</th>
    </tr>

    <tr>
      <td>1</td>
      <td>John Styles</td>
      <td><?= $john ?></td>
    </tr>

    <tr>
      <td>2</td>
      <td>Jane Doe</td>
      <td><?= $jane ?></td>
    </tr>
  </table>
</body>
</html>


<?php
echo "<br />";

$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
  $a += 10;
  $b += 5;

  echo "Step $i: a = $a, b = $b<br>";
}

echo "(For) End of the loop: a = $a, b = $b";
?>

<?php
echo "<br />";
echo "<br />";

$a = 0;
$b = 0;

$i = 0;
while ($i <= 5) {
    $a += 10;
    $b += 5;

    echo "Step $i: a = $a, b = $b<br>";

    $i++;
}

echo "(While) End of the loop: a = $a, b = $b";
?>

<?php
echo "<br />";
echo "<br />";

$a = 0;
$b = 0;

$i = 0;
do {
    $a += 10;
    $b += 5;

    echo "Step $i: a = $a, b = $b<br>";

    $i++;
} while ($i <= 5);

echo "(Do while) End of the loop: a = $a, b = $b";
?>