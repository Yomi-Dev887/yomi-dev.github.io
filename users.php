<?php
include "includes/config.php";
 $url = "users.php";

# PAGE LOGICS

// ADD USERS

if(isset($_POST['addUser'])){
    if(isset($_POST['iAgree'])){
            # data collection
        $user = trim(mysqli_real_escape_string($dbConn, $_POST['user']));
        $password = trim(mysqli_real_escape_string($dbConn, $_POST['password']));
        $password2 = trim(mysqli_real_escape_string($dbConn, $_POST['password2']));

        if(!empty($user)){
            if(strlen($user) > 15){
                $errors[] = $userError = "";
                $error = "maximum of 15 letters expected";
            }
            else{
                #prevent duplicate entry
                $query = mysqli_query($dbConn, "SELECT username FROM users WHERE username='$user'");
                if(mysqli_num_rows($query) > 0){
                    $errors[] = $userError = "";
                    $error = " user '$user' already exists, please modify";
                }
            }
        }
        else{
            $errors[] = $userError = "";
        }
        if(empty($password)){$errors[] = $passwordError = "";}
        if(empty($password2)){$errors[] = $password2Error = "";}
        else{
            if($password != $password2){
                $errors[] = $password2Error="";
                $error = "the two password do not match";
            }
        
        else{
            if(strlen($password2) > 15){
                $errors[] = $password2Error = "";
                $error = "password lenght must not exceed 15 characters";
            }
        }
    }

        # send data to database 
        if(count($errors) == 0){
            $query = mysqli_query( $dbConn, "INSERT INTO users(username, password, dc) VALUES('$user', '$password2' '$now') ");
            if($query){
                $success = "user '$user' added successfully";
                $clearFields = true;
            }
            else{
                $error = "something went wrong".mysqli_error($dbConn);
            }
        }
        else{
            $errorCnt = count($errors);
            $error = (!empty($error)) ? "You have ($errorCnt) unresolved error(s)<br>".$error : "You have ($errorCnt) unresolved error(s)";

        }
    }
    else{
        $error = "Kindly check the conset box to proceed";
    }
}

# Delete user
if(isset($_GET['delete']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $query = mysqli_query($dbConn, "DELETE FROM users WHERE id=$id");
    if($query){
        $success = " User $id deleted successfully";
        header("Refresh: 5; url=users.php");
    }
    else{
        $error = "something went wrong".mysqli_error($dbConn);  
    }
}
# Edit user
if(isset($_GET['edit']) && isset($_GET['id'])){
    $id = $_GET['id'];
    if(isset($_POST['nameEdit'])){
        $user = trim(mysqli_real_escape_string($dbConn, $_POST['nameEdit']));
        if(empty($user)){
            $errors[] = $userEditError = "";
        }
        else{
            #prevent duplicate entry
            $query = mysqli_query($dbConn, "SELECT name FROM users WHERE name='$user' AND id=$id");
            $query2 = mysqli_query($dbConn, "SELECT name FROM users WHERE name='$user' AND id<>$id");
            if(mysqli_num_rows($query) > 0){
                $errors[] = $userEditError = "";
                $error = " Please modify the name to continue";
            }
            if(mysqli_num_rows($query2) > 0){
                $errors[] = $userEditError = "";
                $error = " user '$user' already exists in the Database";
            }
        } 
        # send data to database 
        if(count($errors) == 0){
            $query = mysqli_query( $dbConn, "UPDATE users SET name='$user', du='$now' WHERE id=$id");
            if($query){
                $success = "user has been updated to '$user' successfully";
                header("Refresh: 5; url=users.php");
                
            }
            else{
                $error = "something went wrong".mysqli_error($dbConn);
            }
        }
        else{
            $errorCnt = count($errors);
            $error = (!empty($error)) ? "You have ($errorCnt) unresolved error(s)<br>".$error : "You have ($errorCnt) unresolved error(s)";
        }
    }
   
}

# Truncate Table
if(isset($_GET['truncate'])){
    $prompt = true;
    $promptTitle = "Confirm Truncate";
    $promptMsg = "Are you sure you want to truncate the entire 'Users' Table?";
    $buttonText = "Yes, proceed";
   if(isset($_POST['doPrompt'])){
       $prompt = false;
       $query = mysqli_query($dbConn, "TRUNCATE users");
       if($query){
           $success = "All table data cleared successfully";
           header("Refresh: 4; url=$url");
       }
       else{
            $error = "Something went wrong".mysqli_error($dbConn);
        }
            
   }
    
}
# activate
if(isset($_GET['activate'])){
    $id = $_GET['id'];
   $query = mysqli_query($dbConn, "UPDATE users SET status='active' WHERE id='$id'");
       if($query){
           $success = "activation of user done successfully";
           header("Refresh: 4; url=$url");
       }
       else{
            $error = "Something went wrong".mysqli_error($dbConn);
        }
    
}
# Deactivate
if(isset($_GET['deactivate'])){
    $id = $_GET['id'];
   $query = mysqli_query($dbConn, "UPDATE users SET status='inactive' WHERE id='$id'");
       if($query){
           $success = "deactivation of user done successfully";
           header("Refresh: 4; url=$url");
       }
       else{
            $error = "Something went wrong".mysqli_error($dbConn);
        }
    
}

# MultiDelete
if(isset($_POST['multiDelete'])){
    if(isset($_POST['userIds']) && count($_POST['userIds']) > 0){
        $ids = $_POST['userIds'];
        $idCnt = count($ids);
        if($idCnt < 6 && $idCnt > 0){
            foreach($ids as $id){
                $query = mysqli_query($dbConn, "DELETE FROM users WHERE id=$id");
                $queryResults[] = $query;
            }
            if(count($queryResults) > 0){
                $falseFeedback = 0;
                foreach($queryResults as $result){
                    if(!$result){ $falseFeedback++; }
                }
                if($falseFeedback > 0 && $falseFeedback < $idCnt){
                    $success = "Action successful but not all entries where deleted";
                } 
                elseif($falseFeedback == $idCnt){
                    $error = "Something went wrong, please try again Later";
                }
                else{
                    $success = "Selected records were deleted successfully";
                }
            }
        }
        else{
            $error = "maximum of 5 rows can be deleted at a <strong>GO</strong>";
        }
    }
    else{
        $error = "Please select at least one (1) record using the checkbox to proceed";
    }
}

const TITLE = "Manage Users";
const HEADER = "Users ";
const PAGE_LINK = "users";

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
                                <h4 class="mb-0 card-title">Add User</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <fieldset class="">
                                        <legend class="legend">User Information</legend>
                                        <p>The fields below are required including the two boxes</p>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-person-badge text-theme"></i></span>
                                            <input type="text" placeholder="username" name="user" class="form-control <?= isset($userError) ? 'border-danger' : '' ?>" value=" <?php if($clearFields){ echo ''; }elseif(isset($_POST['user'])){ echo $_POST['user']; }else{ echo '';} ?>">
                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">Password</span>
                                            <input type="password" name="password" class="form-control <?= isset($passwordError) ? 'border-danger' : '' ?>" value=" <?php if($clearFields){ echo ''; }elseif(isset($_POST['password'])){ echo $_POST['password']; }else{ echo '';} ?>">
                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">Password again</span>
                                            <input type="password" name="password2" class="form-control <?= isset($password2Error) ? 'border-danger' : '' ?>" value=" <?php if($clearFields){ echo ''; }elseif(isset($_POST['password2'])){ echo $_POST['password2']; }else{ echo '';} ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label for="">
                                                <input type="checkbox" name="iAgree" class="form-check-inline" name="">
                                                I agree to continue
                                            </label>
                                        </div>
                                        <div class="">
                                            <button type="submit" name="addUser" class="btn  btn-success rounded-pill"><i class="bi bi-check-circle me-2"></i>Add</button>
                                            <button type="reset" class="btn btn-outline-danger rounded-pill"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <form action="" method="post">
                            <div class="card shadow border-0">
                                <div class="card-header bg-theme">
                                    <h4 class="mb-0 card-title">Manage Users</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive overflow-auto">

                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><i class="bi bi-list-ol text-secondary"></i></th>
                                                    <th><i class="bi bi-check-square"></i></th>
                                                    <th><i class="bi bi-toggles2"></i></th>
                                                    <th>Username</th>
                                                    <th scope="col"><i class="bi bi-calendar text-theme"></i></th>
                                                    <th scope="col"><i class="bi bi-calendar2 text-primary"></i></th>
                                                    <th scope="col"><i class="bi bi-toggle-on text-success"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                            $no = 1;
                                            $query = mysqli_query($dbConn, "SELECT * FROM users");
                                            while($record = mysqli_fetch_assoc($query)){
                                            
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?= $no++ ?>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" name="userIds[]" value="<?= $record['id'] ?>">
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <?php if($record['status'] != 'active'): ?>
                                                            <a href="users.php?activate&id=<?= $record['id'] ?>" class="btn btn-sm btn-success" title="<Activate></Activate>"><i class="bi bi-check"></i></a>
                                                            <?php else:  ?>
                                                            <a href="users.php?deactivate&id=<?= $record['id'] ?>" class="btn btn-sm btn-secondary" title="Deactivate"><i class="bi bi-x"></i></a>
                                                            <?php if(isset($_GET['edit']) && $_GET['id'] == $record['id']): ?>
                                                            <a href="users.php" class="btn btn-sm btn-danger" title="Cancel Edit"><i class="bi bi-x"></i></a>
                                                            <?php else: ?>
                                                            <a href="users.php?edit&id=<?= $record['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit <?= $record['username'] ?>"><i class="bi bi-pencil-fill"></i></a>
                                                            <?php endif ?>
                                                            <a href="users.php?delete&id=<?= $record['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete <?= $record['username'] ?>"><i class="bi bi-trash3-fill"></i></a>
                                                            <?php endif ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($_GET['edit']) && (isset($_GET['id']) && $_GET['id'] == $record['id'])): ?>
                                                        <form action="" method="post">
                                                            <input type="text" name="nameEdit" value="<?= (isset($_POST['nameEdit'])) ? $_POST['nameEdit'] : $record['username'] ?>" class="form-control <?= isset($userEditError) ? 'border-danger' : '' ?>">
                                                            <input type="submit" hidden>
                                                        </form>
                                                        <?php else: ?>
                                                        <?= $record['username'] ?>
                                                        <?php endif ?>
                                                    </td>
                                                    <td><?= $record['dc'] ?></td>
                                                    <td><?= $record['du'] ?></td>
                                                    <td>
                                                        <i class="bi bi-circle-fill text-<?= ($record['status'] == 'active') ? 'success' : 'secondary' ?>"></i>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group">
                                        <a href="users.php?truncate" title="Truncate Table" class="btn btn-outline-warning"><i class="bi bi-trash2-fill"></i></a>
                                        <button type="submit" name="multiDelete" title="Delete Multiple" class="btn btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include "includes/foot.php";?>
