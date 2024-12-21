<?php
include "includes/config.php";

# Page Logics
// Add gender
if(isset($_POST['addGender'])){
    # data collection & sanitization
    $gender = trim(mysqli_real_escape_string($dbConn, $_POST['gender']));
    $parent = intval($_POST['parent']);
    
    # data validation
    if(!empty($gender)){
      if(strlen($gender) > 25){
          $errors[] = $genderError = "";
          $error = "maximun of 25 letters expected";
      }  
    }else{
        $errors[] = $genderError = "";
    }
    if($parent == 0){
        $errors[] = $parentError = "";
    }
    # send data to database
    if(count($errors) == 0){
      $success = "All Done";
    }  
    else{
        $errorCnt = count($errors);
        $error = (!empty($error)) ? $error."<br>You have ($errorCnt) unresolved error(s)" : "You have ($errorCnt) unresolved error(s)"; 
    }
}

const TITLE = "Manage Genders";
const HEADER = "Genders";
const PAGE_LINK = "genders";

include "includes/head.php";
?>


<div class="container-fluid">
    <div class="row my-2 mx-1 gx-0">
        <?php include "includes/aside.php";?>
        <div class="col-md-10">
            <?php include "includes/header.php";?>
            <div class="content border-top border-dark border-2 p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card shadow border-0">
                            <div class="card-header bg-theme">
                                <h4 class="mb-0 card-title">New Gender</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <fieldset>
                                        <legend class="legend">Gender Information</legend>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-gender-ambiguous text-theme"></i></span>
                                            <input type="text" name="gender" placeholder="gender name" class="form-control <?= isset($genderError) ? 'border-danger' : '' ?>" value="<?= ($_POST['gender']) ?? '' ?>">
                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-list-ul text-theme"></i></span>
                                            <select name="parent" id="" class="form-select">
                                                <option value="">Select Gender</option>
                                                <?php
                                            $query = mysqli_query($dbConn, "SELECT * FROM genders");
                                            while($record = mysqli_fetch_assoc($query)): ?>
                                                <option value="<?= $record['id'] ?>"><?= $record['name'] ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="">
                                                <input type="checkbox" class="form-check-inline" name="iAgree">I agree to proceed
                                            </label>
                                        </div>
                                        <div class="">
                                            <button type="submit" class="btn btn-success rounded-pill" name="addGender"><i class="bi bi-check-circle me-2"></i>Add</button>
                                            <button type="reset" class="btn btn-outline-danger rounded-pill"><i class="bi bi-x circle me-2"></i>Cancel</button>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow border-0">
                            <div class="card-header bg-theme">
                                <h4 class="mb-0 card-title">Manage Genders</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive overflow-auto">
                                    <table class="table table-stripped table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col"><i class="bi bi-list-ol text-secondary"></i></th>
                                                <th>Name</th>
                                                <th>Parent</th>
                                                <th scope="col"><i class="bi bi-calendar text-theme"></i></th>
                                                <th scope="col"><i class="bi bi-calendar3 text-primary"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $query = mysqli_query($dbConn, "SELECT * FROM genders");
                                            while($record = mysqli_fetch_assoc($query)){
                                                
                                            
                                            ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $record['name'] ?></td>
                                                <td><?= $record['parent'] ?></td>
                                                <td><?= $record['dc'] ?></td>
                                                <td><?= $record['du'] ?></td>
                                            </tr>
                                            <?php }?>
                                        </tbody>

                                    </table>
                                </div>

                            </div>
                            <div class="card-footer">
                                <div class="btn-group">
                                    <button class="btn btn-outline-warning" title="Truncate Table"><i class="bi bi-trash2-fill"></i></button>
                                    <button class="btn btn-outline-danger" title="Clear Table Datau"><i class="bi bi-trash3-fill"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "includes/foot.php";?>
