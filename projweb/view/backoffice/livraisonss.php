<?php
require_once '../../config.php';
require_once '../../model/livraisons.php';
// Initialize database connection first
$db = config::getConnexion();

// Initialize error message variable
$error = '';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['id'])) {
  $id = $_POST['id'];
  $livraisonToDelete = new Livraison("", "", "", "","");
  $livraisonToDelete->setId($id);
  try {
    $livraisonToDelete->delete($db);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
  }
}

// Handle update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  try {
    $id = $_POST['id_liv'];
    $date_liv = $_POST['date_liv'];
    $lieu = $_POST['lieu'];
    $tel = $_POST['tel'];
    $moy_transport = $_POST['moy_transport'];

    // Input validation
    if (empty($date_liv) || empty($lieu) || empty($tel) || empty($moy_transport)) {
      $error = "All fields are required.";
    } elseif (!preg_match('/^\d{8}$/', $tel)) {
      $error = "Phone number must be exactly 8 digits.";
    } elseif (!strtotime($date_liv)) {
      $error = "Invalid date format.";
    }

if (empty($error)) {
      $livraisonToUpdate = new Livraison($date_liv, $lieu, $tel, $moy_transport, $colis);
      $livraisonToUpdate->setId($id);
      $livraisonToUpdate->update($db);
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    }
  } catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
  }
}

// Handle create action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  try {
    $id = $_POST['id_liv'];
    $colis = $_POST['colis'] ?? '';
    $date_liv = $_POST['date_liv'];
    $lieu = $_POST['lieu'];
    $tel = $_POST['tel'];
    $moy_transport = $_POST['moy_transport'];

    // Input validation
    if (empty($date_liv) || empty($lieu) || empty($tel) || empty($moy_transport)) {
      $error = "All fields are required.";
    } elseif (!preg_match('/^\d{8}$/', $tel)) {
      $error = "Phone number must be exactly 8 digits.";
    } elseif (!strtotime($date_liv)) {
      $error = "Invalid date format.";
    }

    if (empty($error)) {
      $livraisonToCreate = new Livraison($id, $colis, $date_liv, $lieu, $tel, $moy_transport);
      $livraisonToCreate->create($db);
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    }
  } catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
  }
}

if (!isset($error)) {
  $error = '';
}
$sortOrder = 'ASC';
$searchField = '';
$searchQuery = '';
$showStats = false;
$deliveryCount = 0;

if (isset($_POST['sort_order']) && in_array($_POST['sort_order'], ['ASC', 'DESC'])) {
    $sortOrder = $_POST['sort_order'];
}

if (isset($_POST['search_field']) && in_array($_POST['search_field'], ['id_liv', 'lieu', 'tel'])) {
    $searchField = $_POST['search_field'];
}

if (isset($_POST['search_query'])) {
    $searchQuery = trim($_POST['search_query']);
}

if (isset($_POST['show_stats'])) {
    $showStats = true;
    // Get count of delivery IDs
    $countQuery = "SELECT COUNT(id_liv) as count FROM livraisons";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute();
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $deliveryCount = $countResult ? (int)$countResult['count'] : 0;
}

$sql = "SELECT * FROM livraisons";
$params = [];

if ($searchField && $searchQuery !== '') {
    $sql .= " WHERE $searchField LIKE :searchQuery";
    $params['searchQuery'] = '%' . $searchQuery . '%';
}

$sql .= " ORDER BY id_liv $sortOrder";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$livraisonList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <style>
    body {
      background-color: #e8f5e9;
      /* Light green background */
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
      /* Violet header */
      color: white;
    }

    .modal-footer {
      background-color: #e8f5e9;
    }
  </style>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Free Bootstrap Admin Template : Two Page</title>
  <!-- BOOTSTRAP STYLES-->
  <link href="assets/css/bootstrap.css" rel="stylesheet" />
  <!-- FONTAWESOME STYLES-->
  <link href="assets/css/font-awesome.css" rel="stylesheet" />
  <!-- CUSTOM STYLES-->
  <link href="assets/css/custom.css" rel="stylesheet" />
  <!-- GOOGLE FONTS-->
  <link
    href="http://fonts.googleapis.com/css?family=Open+Sans"
    rel="stylesheet"
    type="text/css" />
</head>

<body>
  <div id="wrapper">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="adjust-nav">
        <div class="navbar-header">
          <button
            type="button"
            class="navbar-toggle"
            data-toggle="collapse"
            data-target=".sidebar-collapse">
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
    <!-- /. NAV TOP  -->
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
    <!-- /. NAV SIDE  -->
    <div id="page-wrapper">
      <div id="page-inner">
        <div class="row">
          <div class="col-md-12">
            <h2>Delivery database</h2>
          </div>
        </div>
        <!-- /. ROW  -->
        <hr />
        <form method="POST" class="form-inline mb-3" style="gap: 0.5rem;">
          <label for="search_field" class="mr-2 font-weight-bold">Rechercher par:</label>
          <select name="search_field" id="search_field" class="form-control mr-2">
            <option value="id_liv" <?php if ($searchField === 'id_liv') echo 'selected'; ?>>ID</option>
            <option value="lieu" <?php if ($searchField === 'lieu') echo 'selected'; ?>>Place of Delivery</option>
            <option value="tel" <?php if ($searchField === 'tel') echo 'selected'; ?>>Phone Number</option>
          </select>
          <input type="text" name="search_query" class="form-control mr-2" placeholder="Search..." value="<?php echo htmlspecialchars($searchQuery); ?>" />
          <label for="sort_order" class="mr-2 font-weight-bold">Trier:</label>
          <select name="sort_order" id="sort_order" class="form-control mr-2">
            <option value="ASC" <?php if ($sortOrder === 'ASC') echo 'selected'; ?>>Croissant</option>
            <option value="DESC" <?php if ($sortOrder === 'DESC') echo 'selected'; ?>>Décroissant</option>
          </select>
          <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
        <!-- /. TABLE  ------------------------------------------------------------------------------------->

        <div class="table-container">
          <table class="table table-bordered">
            <thead class="thead-dark">
              <tr>
                <th>ID</th>
                <th>Date of Delivery</th>
                <th>Place of Delivery</th>
                <th>Phone Number</th>
                <th>Transport Mode</th>
                <th>idcolis</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($livraisonList as $liv): ?>
                <tr>
                  <td><?= htmlspecialchars($liv['id_liv']) ?></td>
                  <td><?= htmlspecialchars($liv['date_liv']) ?></td>
                  <td><?= htmlspecialchars($liv['lieu']) ?></td>
                  <td><?= htmlspecialchars($liv['tel']) ?></td>
                  <td><?= htmlspecialchars($liv['moy_transport']) ?></td>
                  <td><?= htmlspecialchars($liv['colis_id']) ?></td>
                  <td>
                    <button class="btn update-btn" style="background-color: #8b7bff; color: white; padding: 5px 10px;"
                      data-toggle="modal"
                      data-target="#updateModal"
                      data-id="<?= htmlspecialchars($liv['id_liv']) ?>"
                      data-date="<?= htmlspecialchars($liv['date_liv']) ?>"
                      data-lieu="<?= htmlspecialchars($liv['lieu']) ?>"
                      data-tel="<?= htmlspecialchars($liv['tel']) ?>"
                      data-transport="<?= htmlspecialchars($liv['moy_transport']) ?>"
                      data-colis="<?= htmlspecialchars($liv['colis_id']) ?>">
                      Update
                    </button>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="id" value="<?= $liv['id_liv'] ?>">
                      <button type="submit" class="btn" style="background-color: #ff0000; color: white; padding: 5px 10px;" name="delete">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Calendar of Delivery Dates -->
        <div id="deliveryCalendar" style="max-width: 600px; margin: 2rem auto;">
          <h3>Calendrier des dates de livraison (Delivery Dates)</h3>
          <div style="margin-bottom: 1rem;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: #2196f3; margin-right: 8px; vertical-align: middle;"></span>
            <span>Dates de livraison (Delivery Dates)</span>
          </div>
          <div id="calendar"></div>
        </div>
        <!-- /. TABLE  ------------------------------------------------------------------------------------->

        <!-- Modal for Update -->
        <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="updateModalLabel">Update Vehicle Rental</h4>
              </div>
              <div class="modal-body">
                <form id="updateForm" method="POST">
                  <input type="hidden" name="update" value="1">
                  <input type="hidden" name="id_liv" id="update_id">
                  <input type="hidden" name="colis" id="update_colis">

                <div class="form-group">
                    <label for="date_liv">Date of Delivery</label>
                    <input type="date" class="form-control" id="update_date" name="date_liv" required>
                </div>

                <div class="form-group">
                    <label for="lieu">Place of Delivery</label>
                    <input type="text" class="form-control" id="update_lieu" name="lieu" required>
                </div>

                <div class="form-group">
                    <label for="tel">Phone Number</label>
                    <input type="tel" class="form-control" id="update_tel" name="tel" required pattern="[0-9]{8}" title="Enter a valid phone number with exactly 8 digits">
                </div>

                <div class="form-group">
                    <label for="moy_transport">Transport Mode</label>
                    <input type="text" class="form-control" id="update_transport" name="moy_transport" required>
                </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn" style="background-color: #8b7bff; color: white;">Update</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script>
          $(document).ready(function() {
            $('.update-btn').click(function() {
              var button = $(this);
              $('#update_id').val(button.data('id'));
              $('#update_date').val(button.data('date'));
              $('#update_lieu').val(button.data('lieu'));
              $('#update_tel').val(button.data('tel'));
              $('#update_transport').val(button.data('transport'));
              $('#update_colis').val(button.data('colis'));
            });
          });
        </script>
        <!-- Modal for Update -->

        <!-- Include FullCalendar CSS and JS -->
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

        <script>
          $(document).ready(function() {
            // Prepare delivery dates for calendar events
            var deliveryDates = [
              <?php
                foreach ($livraisonList as $liv) {
                  $dateLiv = htmlspecialchars($liv['date_liv']);
                  $id = htmlspecialchars($liv['id_liv']);
                  $lieu = htmlspecialchars($liv['lieu']);
                  $tel = htmlspecialchars($liv['tel']);
                  $transport = htmlspecialchars($liv['moy_transport']);
                  // Change title to show the delivery date itself
                  echo "{ title: '$dateLiv', start: '$dateLiv', color: '#2196f3', extendedProps: { lieu: '$lieu', tel: '$tel', transport: '$transport' } },";
                }
              ?>
            ];

            // Initialize FullCalendar
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
              initialView: 'dayGridMonth',
              initialDate: new Date(new Date().getFullYear(), 4, 1), // May 1st of current year
              height: 400,
              events: deliveryDates,
              headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,dayGridDay'
              },
              eventDidMount: function(info) {
                var tooltip = new Tooltip(info.el, {
                  title: 'ID: ' + info.event.title + '\\nPlace: ' + info.event.extendedProps.lieu + '\\nPhone: ' + info.event.extendedProps.tel + '\\nTransport: ' + info.event.extendedProps.transport,
                  placement: 'top',
                  trigger: 'hover',
                  container: 'body'
                });
              }
            });
            calendar.render();
          });
        </script>

        <!-- /. ROW  -->
      </div>
      <!-- /. PAGE INNER  -->
    </div>
    <!-- /. PAGE WRAPPER  -->
  </div>
  <!-- /. WRAPPER  -->
  <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
</body>

</html>
