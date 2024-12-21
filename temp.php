<?php
include "includes/config.php";

const TITLE = "Template Home";
const HEADER = "Template ";
const PAGE_LINK = "template";

include "includes/head.php";
?>


<div class="container-fluid">
    <div class="row my-2 mx-1 gx-0">
        <?php include "includes/aside.php";?>
        <div class="col-md-10">
            <?php include "includes/header.php";?>
            <div class="content border-top border-danger border-2 p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card shadow border-0">
                            <div class="card-header bg-theme">
                                <h4 class="mb-0 card-title">Add Items</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <fieldset>
                                        <legend class="legend">Title</legend>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-person-fill text-theme"></i></span>
                                            <input type="text" name="user" class="form-control">
                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-lock-fill text-theme"></i></span>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-list-columns-reverse text-theme"></i></span>
                                            <select name="" id="" class="form-select">
                                                <option value="">Items</option>
                                                <option value="">Items</option>
                                                <option value="">Items</option>
                                            </select>
                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-textarea text-theme"></i></span>
                                            <textarea name="" id="" cols="30" rows="10" class="form-control"></textarea>

                                        </div>
                                        <div class="mb-4">
                                            <label for="">
                                                <input type="checkbox" class="form-check-inline" name="">I agree to proceed
                                            </label>
                                        </div>
                                        <div class="">
                                            <button type="submit" class="btn btn-success rounded-pill">Add</button>
                                            <button type="reset" class="btn btn-outline-danger rounded-pill">Cancel</button>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow border-0">
                            <div class="card-header bg-theme">
                                <h4 class="mb-0 card-title">Manage Items</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive overflow-auto">
                                    <table class="table table-stripped table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col"><i class="bi bi-list-ol text-secondary"></i></th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th scope="col"><i class="bi bi-calendar text-theme"></i></th>
                                                <th scope="col"><i class="bi bi-calendar3 text-primary"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>cell</td>
                                                <td>cell</td>
                                                <td>cell</td>
                                                <td>cell</td>
                                                <td>cell</td>
                                            </tr>
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
