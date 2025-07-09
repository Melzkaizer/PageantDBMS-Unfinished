<?php
// score/admin_view.php

ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

require_once '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$criteria_result = $conn->query("SELECT id, name FROM criteria ORDER BY name ASC");
$contestants_result = $conn->query("SELECT id, name, photo FROM contestants ORDER BY name ASC");
$judges_result = $conn->query("SELECT id, name FROM judges ORDER BY name ASC");

$criteria_options = [];
while ($row = $criteria_result->fetch_assoc()) {
  $criteria_options[] = $row;
}

$contestants = [];
while ($row = $contestants_result->fetch_assoc()) {
  $contestants[] = $row;
}

$judges = [];
while ($row = $judges_result->fetch_assoc()) {
  $judges[$row['id']] = $row['name'];
}

$filter_criterion_id = isset($_GET['criterion_id']) ? intval($_GET['criterion_id']) : null;
$action = $_GET['action'] ?? '';

if ($action === 'export_csv') {
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="scores_export.csv"');
  $output = fopen('php://output', 'w');

  $header = ['Contestant'];
  foreach ($criteria_options as $c) {
    foreach ($judges as $jid => $jname) {
      $header[] = $c['name'] . " - " . $jname;
    }
  }
  $header[] = "Total Score";
  fputcsv($output, $header);

  foreach ($contestants as $con) {
    $row = [$con['name']];
    $total = 0;
    foreach ($criteria_options as $c) {
      foreach ($judges as $jid => $jname) {
        $query = $conn->prepare("SELECT score FROM scores WHERE contestant_id=? AND judge_id=? AND criterion_id=?");
        $query->bind_param("iii", $con['id'], $jid, $c['id']);
        $query->execute();
        $res = $query->get_result()->fetch_assoc();
        $score = isset($res['score']) ? $res['score'] : 0;
        $row[] = $score;
        $total += $score;
      }
    }
    $row[] = round($total, 2);
    fputcsv($output, $row);
  }
  fclose($output);
  exit;
}

if ($action === 'export_csv_criteria') {
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="scores_per_criterion.csv"');
  $output = fopen('php://output', 'w');

  foreach ($criteria_options as $c) {
    fputcsv($output, ["Criterion: " . $c['name']]);
    $header = ['Contestant'];
    foreach ($judges as $jname) {
      $header[] = $jname;
    }
    $header[] = 'Average';
    fputcsv($output, $header);

    foreach ($contestants as $con) {
      $row = [$con['name']];
      $sum = 0;
      $count = 0;
      foreach ($judges as $jid => $jname) {
        $query = $conn->prepare("SELECT score FROM scores WHERE contestant_id=? AND judge_id=? AND criterion_id=?");
        $query->bind_param("iii", $con['id'], $jid, $c['id']);
        $query->execute();
        $res = $query->get_result()->fetch_assoc();
        $score = isset($res['score']) ? $res['score'] : 0;
        $row[] = $score;
        $sum += $score;
        $count++;
      }
      $avg = $count ? round($sum / $count, 2) : 0;
      $row[] = $avg;
      fputcsv($output, $row);
    }
    fputcsv($output, []); // Add a blank line between sections
  }

  fclose($output);
  exit;
}

if ($action === 'export_pdf_criteria') {
  ob_clean();
  $options = new Options();
  $options->set('isHtml5ParserEnabled', true);
  $options->set('isRemoteEnabled', true);
  $dompdf = new Dompdf($options);

  $html = '<h2 style="text-align: center;">Tabulation Results - Per Criterion</h2>';

  foreach ($criteria_options as $c) {
    $html .= '<h3>' . htmlspecialchars($c['name']) . '</h3>';
    $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
    $html .= '<tr><th>Contestant</th>';
    foreach ($judges as $jname) {
      $html .= '<th>' . htmlspecialchars($jname) . '</th>';
    }
    $html .= '<th>Average</th></tr>';

    foreach ($contestants as $con) {
      $html .= '<tr><td>' . htmlspecialchars($con['name']) . '</td>';
      $sum = 0;
      $count = 0;
      foreach ($judges as $jid => $jname) {
        $query = $conn->prepare("SELECT score FROM scores WHERE contestant_id=? AND judge_id=? AND criterion_id=?");
        $query->bind_param("iii", $con['id'], $jid, $c['id']);
        $query->execute();
        $res = $query->get_result()->fetch_assoc();
        $score = isset($res['score']) ? $res['score'] : 0;
        $html .= '<td>' . $score . '</td>';
        $sum += $score;
        $count++;
      }
      $avg = $count ? round($sum / $count, 2) : 0;
      $html .= '<td>' . $avg . '</td></tr>';
    }
    $html .= '</table><br><br>';
  }

  $html .= '<br><table style="width:100%; text-align: center;"><tr>';
  $html .= '<td>_________________________<br>Chairperson</td>';
  $html .= '<td>_________________________<br>Tabulator</td>';
  $html .= '</tr></table>';

  $dompdf->loadHtml($html);
  $dompdf->setPaper('A4', 'landscape');
  $dompdf->render();
  $dompdf->stream("per_criterion_report.pdf", ["Attachment" => true]);
  exit;
}

// Prepare score summary
$scores = [];
foreach ($contestants as $con) {
  $scores[$con['id']] = [
    'name' => $con['name'],
    'total' => 0,
    'criteria' => [],
    'judges' => []
  ];
  foreach ($criteria_options as $c) {
    $query = $conn->prepare("SELECT AVG(score) as avg_score FROM scores WHERE contestant_id=? AND criterion_id=?");
    $query->bind_param("ii", $con['id'], $c['id']);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();
    $avg = is_null($result['avg_score']) ? 0 : round($result['avg_score'], 2);
    $scores[$con['id']]['criteria'][$c['id']] = $avg;
    $scores[$con['id']]['total'] += $avg;
  }

  foreach ($judges as $jid => $jname) {
    $query = $conn->prepare("SELECT criterion_id, score FROM scores WHERE contestant_id=? AND judge_id=?");
    $query->bind_param("ii", $con['id'], $jid);
    $query->execute();
    $result = $query->get_result();
    $judge_scores = [];
    while ($row = $result->fetch_assoc()) {
      $judge_scores[$row['criterion_id']] = $row['score'];
    }
    $scores[$con['id']]['judges'][$jid] = $judge_scores;
  }
}

// Sort scores by total descending
uasort($scores, fn($a, $b) => $b['total'] <=> $a['total']);

if ($action === 'export_pdf') {
  ob_clean();
  $options = new Options();
  $options->set('isHtml5ParserEnabled', true);
  $options->set('isRemoteEnabled', true);
  $dompdf = new Dompdf($options);

  $html = '<h2 style="text-align: center;">Tabulation Results</h2>';
  $html .= '<p style="text-align: center;">Overall Scores</p>';

  $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
  $html .= '<tr><th>Contestant</th>';
  foreach ($criteria_options as $c) {
    $html .= '<th>' . htmlspecialchars($c['name']) . '</th>';
  }
  $html .= '<th>Total</th></tr>';
  foreach ($scores as $s) {
    $html .= '<tr><td>' . htmlspecialchars($s['name']) . '</td>';
    foreach ($criteria_options as $c) {
      $score = $s['criteria'][$c['id']] ?? 0;
      $html .= '<td>' . $score . '</td>';
    }
    $html .= '<td>' . round($s['total'], 2) . '</td></tr>';
  }
  $html .= '</table>';

  $html .= '<br><br><br><table style="width:100%; text-align: center;"><tr>';
  $html .= '<td>_________________________<br>Chairperson</td>';
  $html .= '<td>_________________________<br>Tabulator</td>';
  $html .= '</tr></table>';

  $dompdf->loadHtml($html);
  $dompdf->setPaper('A4', 'landscape');
  $dompdf->render();
  $dompdf->stream("score_report.pdf", ["Attachment" => true]);
  exit;
}

ob_end_flush();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Scores</title>
  <link rel="stylesheet" href="../styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
  <style>
    .chart-container {
      width: 100%;
      max-width: 900px;
      margin: 30px auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 40px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: center;
    }
    th {
      background-color: #f4f4f4;
      cursor: pointer;
    }
  </style>
</head>
<body>
  
<header>
    <h1>Score Overview</h1>
    <nav>
      <a href="../index.php">Home</a>
      <a href="../judges/list.php">Judges</a>
      <a href="../contestants/list.php">Contestants</a>
      <a href="../criteria/list.php">Criteria</a>
      <a href="../scores/add.php">Score Input</a>
      <a href="../scores/view.php">View Scores</a>
      <a href="../reset_password.php">Reset Password</a>
      <a href="../logout.php">Logout</a>
    </nav>
  </header>

  <form method="get" style="margin-bottom: 20px;">
    <label for="criterion_id">Filter by Criterion:</label>
    <select name="criterion_id" id="criterion_id" onchange="this.form.submit()">
      <option value="">-- All Criteria --</option>
      <?php foreach ($criteria_options as $option): ?>
        <option value="<?= $option['id'] ?>" <?= $filter_criterion_id == $option['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($option['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" style="height: 35px; text-align: center;">Apply</button>
  </form>

   <p>
    <a href="?action=export_pdf">Export PDF</a> |
    <a href="?action=export_csv">Export CSV</a> |
    <a href="?action=export_pdf_criteria">Export Per-Criterion PDF</a> |
    <a href="?action=export_csv_criteria">Export Per-Criterion CSV</a>
  </p>

  <div class="chart-container">
    <canvas id="scoreChart"></canvas>
  </div>

  <script>
    const ctx = document.getElementById('scoreChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [
          <?php foreach ($scores as $s): ?>
            "<?= htmlspecialchars($s['name']) ?>",
          <?php endforeach; ?>
        ],
        datasets: [{
          label: "<?= $filter_criterion_id ? htmlspecialchars($criteria_options[array_search($filter_criterion_id, array_column($criteria_options, 'id'))]['name']) : 'Total Score' ?>",
          data: [
            <?php foreach ($scores as $s): ?>
              <?= $filter_criterion_id ? $s['criteria'][$filter_criterion_id] ?? 0 : $s['total'] ?>,
            <?php endforeach; ?>
          ],
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>

  <h3>Judge-Specific Scores</h3>
  <table id="judgeTable">
    <thead>
      <tr>
        <th onclick="sortTable(0)">Contestant</th>
        <?php foreach ($judges as $jname): ?>
          <th><?= htmlspecialchars($jname) ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($scores as $cid => $data): ?>
    <tr>
      <td><?= htmlspecialchars($data['name']) ?></td>
      <?php foreach ($judges as $jid => $jname): ?>
        <td>
          <?php
            $judge_scores = $data['judges'][$jid] ?? [];
            $display = [];
            foreach ($judge_scores as $crit_id => $score) {
              $cname = array_filter($criteria_options, fn($c) => $c['id'] == $crit_id);
              $cname = reset($cname)['name'] ?? '';
              $display[] = "$cname: $score";
            }
            echo implode("<br>", $display);
          ?>
        </td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <script>
    function sortTable(n) {
      const table = document.getElementById("judgeTable");
      let switching = true, dir = "asc", switchcount = 0;
      while (switching) {
        switching = false;
        const rows = table.rows;
        for (let i = 1; i < (rows.length - 1); i++) {
          let shouldSwitch = false;
          const x = rows[i].getElementsByTagName("TD")[n];
          const y = rows[i + 1].getElementsByTagName("TD")[n];
          if ((dir === "asc" && x.innerText.toLowerCase() > y.innerText.toLowerCase()) ||
              (dir === "desc" && x.innerText.toLowerCase() < y.innerText.toLowerCase())) {
            shouldSwitch = true;
            break;
          }
        }
        if (shouldSwitch) {
          rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
          switching = true;
          switchcount++;
        } else {
          if (switchcount === 0 && dir === "asc") {
            dir = "desc";
            switching = true;
          }
        }
      }
    }
  </script>

  <div style="margin-top: 30px;">
    <a href="../index.php">&larr; Back to Dashboard</a>
  </div>
</body>
</html>