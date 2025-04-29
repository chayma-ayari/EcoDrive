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
  $livraisonToDelete = new Livraison("", "", "", "");
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
      $livraisonToUpdate = new Livraison($date_liv, $lieu, $tel, $moy_transport);
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
$searchField = $_GET['search_field'] ?? '';
$searchTerm = $_GET['search_term'] ?? '';
$sortField = $_GET['sort_field'] ?? '';
$sortOrder = $_GET['sort_order'] ?? 'asc';

$allowedSearchFields = ['id_liv', 'lieu', 'moy_transport'];
$allowedSortFields = ['id_liv', 'date_liv', 'lieu', 'tel', 'moy_transport'];
$sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

$whereClause = '';
$params = [];

if (in_array($searchField, $allowedSearchFields) && !empty($searchTerm)) {
    $whereClause = "WHERE $searchField LIKE :searchTerm";
    $params[':searchTerm'] = '%' . $searchTerm . '%';
}

$orderClause = '';
if (in_array($sortField, $allowedSortFields)) {
    $orderClause = "ORDER BY $sortField $sortOrder";
}

$query = "SELECT * FROM livraisons $whereClause $orderClause";
$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

$stmt->execute();
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
        <!-- Search form -->
        <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="form-inline" style="margin-bottom: 15px;">
          <div class="form-group">
            <label for="search_field">Search by: </label>
            <select name="search_field" id="search_field" class="form-control" style="margin-left: 5px; margin-right: 10px;">
              <option value="id_liv" <?= ($searchField == 'id_liv') ? 'selected' : '' ?>>ID</option>
              <option value="lieu" <?= ($searchField == 'lieu') ? 'selected' : '' ?>>Place of Delivery</option>
              <option value="moy_transport" <?= ($searchField == 'moy_transport') ? 'selected' : '' ?>>Transport Mode</option>
            </select>
          </div>
          <div class="form-group">
            <input type="text" name="search_term" class="form-control" placeholder="Enter search term" value="<?= htmlspecialchars($searchTerm) ?>" required>
          </div>
          <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Search</button>
          <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn btn-default" style="margin-left: 10px;">Reset</a>
        </form>
        <!-- Sort form -->
        <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="form-inline" style="margin-bottom: 15px;">
          <div class="form-group">
            <label for="sort_field">Sort by: </label>
            <select name="sort_field" id="sort_field" class="form-control" style="margin-left: 5px; margin-right: 10px;">
              <option value="">-- Select field --</option>
              <option value="id_liv" <?= ($sortField == 'id_liv') ? 'selected' : '' ?>>ID</option>
              <option value="date_liv" <?= ($sortField == 'date_liv') ? 'selected' : '' ?>>Date of Delivery</option>
              <option value="lieu" <?= ($sortField == 'lieu') ? 'selected' : '' ?>>Place of Delivery</option>
              <option value="tel" <?= ($sortField == 'tel') ? 'selected' : '' ?>>Phone Number</option>
              <option value="moy_transport" <?= ($sortField == 'moy_transport') ? 'selected' : '' ?>>Transport Mode</option>
            </select>
          </div>
          <div class="form-group">
            <label for="sort_order">Order: </label>
            <select name="sort_order" id="sort_order" class="form-control" style="margin-left: 5px; margin-right: 10px;">
              <option value="asc" <?= ($sortOrder == 'asc') ? 'selected' : '' ?>>Ascending</option>
              <option value="desc" <?= ($sortOrder == 'desc') ? 'selected' : '' ?>>Descending</option>
            </select>
          </div>
          <input type="hidden" name="search_field" value="<?= htmlspecialchars($searchField) ?>">
          <input type="hidden" name="search_term" value="<?= htmlspecialchars($searchTerm) ?>">
          <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Sort</button>
          <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn btn-default" style="margin-left: 10px;">Reset</a>
        </form>

        <div class="table-container">
          <table class="table table-bordered">
            <thead class="thead-dark">
              <tr>
                <?php
                // Helper function to build sort URLs with toggling order
                function sortUrl($field, $currentSortField, $currentSortOrder, $searchField, $searchTerm) {
                    $order = 'asc';
                    if ($field === $currentSortField) {
                        $order = $currentSortOrder === 'asc' ? 'desc' : 'asc';
                    }
                    $url = htmlspecialchars($_SERVER['PHP_SELF']) . "?sort_field=$field&sort_order=$order";
                    if (!empty($searchField) && !empty($searchTerm)) {
                        $url .= "&search_field=" . urlencode($searchField) . "&search_term=" . urlencode($searchTerm);
                    }
                    return $url;
                }
                ?>
                <th><a href="<?= sortUrl('id_liv', $sortField, $sortOrder, $searchField, $searchTerm) ?>">ID
                    <?php if ($sortField == 'id_liv'): ?>
                        <?= $sortOrder == 'asc' ? '▲' : '▼' ?>
                    <?php endif; ?>
                </a></th>
                <th><a href="<?= sortUrl('date_liv', $sortField, $sortOrder, $searchField, $searchTerm) ?>">Date of Delivery
                    <?php if ($sortField == 'date_liv'): ?>
                        <?= $sortOrder == 'asc' ? '▲' : '▼' ?>
                    <?php endif; ?>
                </a></th>
                <th><a href="<?= sortUrl('lieu', $sortField, $sortOrder, $searchField, $searchTerm) ?>">Place of Delivery
                    <?php if ($sortField == 'lieu'): ?>
                        <?= $sortOrder == 'asc' ? '▲' : '▼' ?>
                    <?php endif; ?>
                </a></th>
                <th><a href="<?= sortUrl('tel', $sortField, $sortOrder, $searchField, $searchTerm) ?>">Phone Number
                    <?php if ($sortField == 'tel'): ?>
                        <?= $sortOrder == 'asc' ? '▲' : '▼' ?>
                    <?php endif; ?>
                </a></th>
                <th><a href="<?= sortUrl('moy_transport', $sortField, $sortOrder, $searchField, $searchTerm) ?>">Transport Mode
                    <?php if ($sortField == 'moy_transport'): ?>
                        <?= $sortOrder == 'asc' ? '▲' : '▼' ?>
                    <?php endif; ?>
                </a></th>
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
                  <td>
<button class="btn update-btn" style="background-color: #28a745; color: white; padding: 5px 10px;"
                      data-toggle="modal"
                      data-target="#updateModal"
                      data-id="<?= htmlspecialchars($liv['id_liv']) ?>"
                      data-date="<?= htmlspecialchars($liv['date_liv']) ?>"
                      data-lieu="<?= htmlspecialchars($liv['lieu']) ?>"
                      data-tel="<?= htmlspecialchars($liv['tel']) ?>"
                      data-transport="<?= htmlspecialchars($liv['moy_transport']) ?>">
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
            });
          });
        </script>
        <!-- Modal for Update -->

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
