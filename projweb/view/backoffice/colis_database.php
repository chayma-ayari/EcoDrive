<?php
require_once '../../config.php';
require_once '../../model/colis.php';

// Initialize database connection
$db = config::getConnexion();

// Initialize error and message variables
$error = '';
$message = '';

// Handle create action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  try {
    $id = $_POST['id_colis'];
    $poids = $_POST['poids'];
    $contenu = $_POST['contenu'];
    $statut = $_POST['statut'];
    $date_envoi = $_POST['date_envoi'];
    $date_livraison = $_POST['date_livraison'];

    // Input validation
    if (empty($id) || empty($poids) || empty($contenu) || empty($statut) || empty($date_envoi) || empty($date_livraison)) {
      $error = "All fields are required.";
    } elseif (!is_numeric($poids) || $poids <= 0) {
      $error = "Weight must be a positive number.";
    } elseif (!strtotime($date_envoi) || !strtotime($date_livraison)) {
      $error = "Invalid date format.";
    }

    if (empty($error)) {
      // Check if id_colis already exists
      $existingColis = Colis::getById($db, $id);
      if ($existingColis) {
        $error = "Colis ID already exists.";
      } else {
        $colisToCreate = new Colis($id, $poids, $contenu, $statut, $date_envoi, $date_livraison);
        $colisToCreate->create($db);
        $message = "Colis created successfully.";
      }
    }
  } catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
  }
}

// Handle update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  try {
    $id = $_POST['id_colis'];
    $poids = $_POST['poids'];
    $contenu = $_POST['contenu'];
    $statut = $_POST['statut'];
    $date_envoi = $_POST['date_envoi'];
    $date_livraison = $_POST['date_livraison'];

    // Input validation
    if (empty($poids) || empty($contenu) || empty($statut) || empty($date_envoi) || empty($date_livraison)) {
      $error = "All fields are required.";
    } elseif (!is_numeric($poids) || $poids <= 0) {
      $error = "Weight must be a positive number.";
    } elseif (!strtotime($date_envoi) || !strtotime($date_livraison)) {
      $error = "Invalid date format.";
    }

    if (empty($error)) {
      // Prevent changing primary key to avoid duplicate entry
      $existingColis = Colis::getById($db, $id);
      if (!$existingColis) {
        $error = "Colis ID does not exist.";
      } else {
        $colisToUpdate = new Colis($id, $poids, $contenu, $statut, $date_envoi, $date_livraison);
        $colisToUpdate->update($db);
        $message = "Colis updated successfully.";
      }
    }
  } catch (Exception $e) {
    if (strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
      $error = "Duplicate entry error: The colis ID already exists.";
    } else {
      $error = 'Error: ' . $e->getMessage();
    }
  }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['id'])) {
  $id = $_POST['id'];
  $colisToDelete = new Colis("", "", "", "", "", "");
  $colisToDelete->setId($id);
  try {
    $colisToDelete->delete($db);
    $message = "Colis deleted successfully.";
  } catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
  }
}

$sortOrder = 'ASC';
$searchField = '';
$searchQuery = '';

if (isset($_POST['sort_order']) && in_array($_POST['sort_order'], ['ASC', 'DESC'])) {
    $sortOrder = $_POST['sort_order'];
}

if (isset($_POST['search_field']) && in_array($_POST['search_field'], ['id_colis', 'contenu', 'statut'])) {
    $searchField = $_POST['search_field'];
}

if (isset($_POST['search_query'])) {
    $searchQuery = trim($_POST['search_query']);
}

$sql = "SELECT * FROM colis";
$params = [];

if ($searchField && $searchQuery !== '') {
    $sql .= " WHERE $searchField LIKE :searchQuery";
    $params['searchQuery'] = '%' . $searchQuery . '%';
}

$sql .= " ORDER BY id_colis $sortOrder";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$colisList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <style>
    body {
      background-color: #e8f5e9;
    }

    /* Green and white table styling */
    .table.table-bordered {
      background-color: white;
    }

    .table.table-bordered thead {
      background-color: #4caf50; /* Green header */
      color: white;
    }

    .table.table-bordered tbody tr:nth-child(odd) {
      background-color: #e8f5e9; /* Light green rows */
    }

    .table.table-bordered tbody tr:nth-child(even) {
      background-color: white; /* White rows */
    }

    .modal-header {
      background-color: #8b7bff;
      color: white;
    }

    .modal-footer {
      background-color: #e8f5e9;
    }
  </style>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Colis Database</title>
  <link href="assets/css/bootstrap.css" rel="stylesheet" />
  <link href="assets/css/font-awesome.css" rel="stylesheet" />
  <link href="assets/css/custom.css" rel="stylesheet" />
  <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
</head>

<body>
  <div id="wrapper">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="adjust-nav">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><i class="fa fa-square-o"></i>&nbsp;TWO PAGE</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">See Website</a></li>
            <li><a href="#">Open Ticket</a></li>
            <li><a href="#">Report Bug</a></li>
          </ul>
        </div>
      </div>
    </div>
    <nav class="navbar-default navbar-side" role="navigation">
      <div class="sidebar-collapse">
        <ul class="nav" id="main-menu">
          <li class="text-center user-image-back">
            <img src="assets/img/find_user.png" class="img-responsive" />
          </li>
          <li>
            <a href="index.html"><i class="fa fa-desktop"></i>Dashboard</a>
          </li>
          <li>
            <a href="livraisonss.php"><i class="fa fa-table"></i>Delivery database</a>
          </li>
          <li>
            <a href="colis_database.php"><i class="fa fa-table"></i>Colis database</a>
          </li>
        </ul>
      </div>
    </nav>
    <div id="page-wrapper">
      <div id="page-inner">
        <div class="row">
          <div class="col-md-12">
            <h2>Colis database</h2>
          </div>
        </div>
        <hr />
        <form method="POST" class="form-inline mb-3" style="gap: 0.5rem;">
          <label for="search_field" class="mr-2 font-weight-bold">Rechercher par:</label>
          <select name="search_field" id="search_field" class="form-control mr-2">
            <option value="id_colis" <?php if ($searchField === 'id_colis') echo 'selected'; ?>>Colis ID</option>
            <option value="contenu" <?php if ($searchField === 'contenu') echo 'selected'; ?>>Content Description</option>
            <option value="statut" <?php if ($searchField === 'statut') echo 'selected'; ?>>Status</option>
          </select>
          <input type="text" name="search_query" class="form-control mr-2" placeholder="Search..." value="<?php echo htmlspecialchars($searchQuery); ?>" />
          <label for="sort_order" class="mr-2 font-weight-bold">Trier:</label>
          <select name="sort_order" id="sort_order" class="form-control mr-2">
            <option value="ASC" <?php if ($sortOrder === 'ASC') echo 'selected'; ?>>Croissant</option>
            <option value="DESC" <?php if ($sortOrder === 'DESC') echo 'selected'; ?>>Décroissant</option>
          </select>
          <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
        <?php if ($message): ?>
          <div class="alert alert-success text-center font-weight-bold" role="alert" style="font-size: 1.2em;">
            <?php echo $message; ?>
          </div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger text-center font-weight-bold" role="alert" style="font-size: 1.2em;">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>
        <div class="table-container">
          <table class="table table-bordered">
            <thead class="thead-dark">
              <tr>
                <th>Colis ID</th>
                <th>Weight (kg)</th>
                <th>Content Description</th>
                <th>Status</th>
                <th>Send Date</th>
                <th>Delivery Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($colisList as $colis): ?>
                <tr>
                  <td><?= htmlspecialchars($colis['id_colis']) ?></td>
                  <td><?= htmlspecialchars($colis['poids']) ?></td>
                  <td><?= htmlspecialchars($colis['contenu']) ?></td>
                  <td><?= htmlspecialchars($colis['statut']) ?></td>
                  <td><?= htmlspecialchars($colis['date_envoi']) ?></td>
                  <td><?= htmlspecialchars($colis['date_livraison']) ?></td>
                  <td>
                    <button class="btn update-btn" style="background-color: #8b7bff; color: white; padding: 5px 10px;"
                      data-toggle="modal"
                      data-target="#updateModal"
                      data-id="<?= htmlspecialchars($colis['id_colis']) ?>"
                      data-poids="<?= htmlspecialchars($colis['poids']) ?>"
                      data-contenu="<?= htmlspecialchars($colis['contenu']) ?>"
                      data-statut="<?= htmlspecialchars($colis['statut']) ?>"
                      data-dateenvoi="<?= htmlspecialchars($colis['date_envoi']) ?>"
                      data-datelivraison="<?= htmlspecialchars($colis['date_livraison']) ?>">
                      Update
                    </button>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="id" value="<?= $colis['id_colis'] ?>">
                      <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Calendar of Delivery Dates -->
        <div id="deliveryCalendar" style="max-width: 600px; margin: 2rem auto;">
          <h3>Calendrier des dates d'envoi (Send Dates) des colis</h3>
          <div style="margin-bottom: 1rem;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: #4caf50; margin-right: 8px; vertical-align: middle;"></span>
            <span>Dates d'envoi (Send Dates)</span>
          </div>
          <div id="calendar"></div>
        </div>

        <!-- Modal for Update -->
        <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="updateModalLabel">Update Colis</h4>
              </div>
              <div class="modal-body">
                <form id="updateForm" method="POST">
                  <input type="hidden" name="update" value="1">
                  <input type="hidden" name="id_colis" id="update_id">

                  <div class="form-group">
                    <label for="poids">Weight (kg)</label>
                    <input type="number" step="0.01" class="form-control" id="update_poids" name="poids" required min="0" />
                  </div>

                  <div class="form-group">
                    <label for="contenu">Content Description</label>
                    <input type="text" class="form-control" id="update_contenu" name="contenu" required />
                  </div>

                  <div class="form-group">
                    <label for="statut">Status</label>
                    <input type="text" class="form-control" id="update_statut" name="statut" required />
                  </div>

                  <div class="form-group">
                    <label for="date_envoi">Send Date</label>
                    <input type="date" class="form-control" id="update_dateenvoi" name="date_envoi" required />
                  </div>

                  <div class="form-group">
                    <label for="date_livraison">Delivery Date</label>
                    <input type="date" class="form-control" id="update_datelivraison" name="date_livraison" required />
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Create new colis form -->
        <!-- Removed as per user request -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

        <!-- Include a simple calendar library (e.g., FullCalendar) -->
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

        <script>
          $(document).ready(function() {
            $('.update-btn').click(function() {
              var button = $(this);
              $('#update_id').val(button.data('id'));
              $('#update_poids').val(button.data('poids'));
              $('#update_contenu').val(button.data('contenu'));
              $('#update_statut').val(button.data('statut'));
              $('#update_dateenvoi').val(button.data('dateenvoi'));
              $('#update_datelivraison').val(button.data('datelivraison'));
            });

            // Prepare delivery dates and delivery dates for calendar events with details
            var deliveryDates = [
              <?php
                foreach ($colisList as $colis) {
                  $dateEnvoi = htmlspecialchars($colis['date_envoi']);
                  $id = htmlspecialchars($colis['id_colis']);
                  $statut = htmlspecialchars($colis['statut']);
                  $poids = htmlspecialchars($colis['poids']);
                  $contenu = htmlspecialchars($colis['contenu']);
                  echo "{ title: 'Date d\\'envoi: $id', start: '$dateEnvoi', color: '#4caf50', extendedProps: { statut: '$statut', poids: '$poids', contenu: '$contenu', type: 'envoi' } },";
                }
              ?>
            ];

            // Initialize FullCalendar
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
              initialView: 'dayGridMonth',
              initialDate: new Date(new Date().getFullYear(), 4, 1), // May 1st of current year (month is 0-indexed)
              height: 400,
              events: deliveryDates,
              headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,dayGridDay'
              },
              eventDidMount: function(info) {
                var tooltip = new Tooltip(info.el, {
                  title: 'ID: ' + info.event.title + '\\nStatus: ' + info.event.extendedProps.statut + '\\nWeight: ' + info.event.extendedProps.poids + ' kg\\nContent: ' + info.event.extendedProps.contenu,
                  placement: 'top',
                  trigger: 'hover',
                  container: 'body'
                });
              }
            });
            calendar.render();
          });
        </script>
      </div>
    </div>
  </div>
</body>

</html>
