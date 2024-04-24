@extends('admin/layouts.app')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Coupon Code</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{ route ('coupons.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" method="post" id="couponCodeForm" name="couponCodeForm">
						<div class="card">
							<div class="card-body">								
								<div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="code">Code</label>
                                        <input type="text" name="code" id="code" class="form-control" placeholder="Coupon Code" autocomplete="true">
                                        <p></p>
                                    </div>
                                </div>      

									<div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Name</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Coupon Code Name">	
                                            <p></p>
										</div> 
									</div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
											<label for="max_uses">Max Uses</label>
											<input type="number" name="max_uses" id="max_uses" placeholder="max Uses" class="form-control">	
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
											<label for="max_uses_user">Max Uses user</label>
											<input type="text" name="max_uses_user" id="max_uses_user" placeholder="max Uses User" class="form-control">	
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="type">Type</label>
											<select name="type" id="type" class="form-control">
                                                <option value="percent">Percent</option>
                                                <option value="fixed">Fixed</option>
                                            </select>
                                            <p></p>
										</div> 
									</div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
											<label for="discount_amount">Dicount Amount</label>
											<input type="text" name="discount_amount" id="discount_amount" placeholder="Dicount Amount" class="form-control">	
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
											<label for="discount_amount">Min Amount</label>
											<input type="text" name="min_amount" id="min_amount" placeholder="Min Amount" class="form-control">	
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="status">Status</label>
											<select name="status" id="status" class="form-control">
                                                <option value="1">Active</option>
                                                <option value="0">Block</option>
                                            </select>
                                            <p></p>
										</div> 
									</div>	
                                    <div class="col-md-6">
                                        <div class="mb-3">
											<label for="starts_at">Starts At</label>
											<input type="text" name="starts_at" id="starts_at" placeholder="Starts At" class="form-control">	
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
											<label for="expires_at">Expires At</label>
											<input type="text" name="expires_at" id="expires_at" placeholder="Expires At" class="form-control">	
                                            <p></p>
                                        </div>
                                    </div>	
                                    <div class="col-md-6">
                                        <div class="mb-3">
											<label for="Description">Description</label>
											<textarea name="Description" id="description" cols="30" rows="5" class="form-control"></textarea>	
                                            <p></p>
                                        </div>
                                    </div>						
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Create</button>
							<a href="{{ route ('categories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
						    </div>
                        </form>
					</div>
					<!-- /.card -->
				</section>
				<!-- /.content -->

                @endsection

                @section('customJs')
                    <script>

                        $(document).ready(function(){
                                 $('#starts_at').datetimepicker({
                                    // options here
                                format:'Y-m-d H:i:s',
                            });
                            $('#expires_at').datetimepicker({
                                    // options here
                                format:'Y-m-d H:i:s',
                            });
                        });

                        $("#couponCodeForm").submit(function(event){
                            event.preventDefault();
                            var element = $(this);

                            $("button[type=submit]").prop('disabled',true);

                            $.ajax({
                                url: '{{ route("coupons.store") }}',
                                type: 'post',
                                data: element.serializeArray(),
                                dataType: 'json',
                                success: function(response){

                                    $("button[type=submit]").prop('disabled',false);

                                    if (response["status"] == true){

                                        window.location.href="{{ route('coupons.index') }}";

                                        $("#code").removeClass('is-invalid')
                                        .siblings('p')
                                        .removeClass('invalid-feedback').html("");

                                        $("#type").removeClass('is-invalid')
                                        .siblings('p')
                                        .removeClass('invalid-feedback').html("")

                                        $("#discount_amount").removeClass('is-invalid')
                                        .siblings('p')
                                        .removeClass('invalid-feedback').html("")

                                        $("#status").removeClass('is-invalid')
                                        .siblings('p')
                                        .removeClass('invalid-feedback').html("")

                                    } else{

                                        var errors = response['errors'];
                                    if (errors['code']) {

                                        $("#code").addClass('is-invalid').siblings('p')
                                        .addClass('invalid-feedback').html(errors['code']);
                                    } else{

                                        $("#code").removeClass('is-invalid').siblings('p')
                                        .removeClass('invalid-feedback').html("");
                                    }

                                    if (errors['type']) {

                                        $("#type").addClass('is-invalid').siblings('p')
                                        .addClass('invalid-feedback').html(errors['type']);
                                    } else{

                                        $("#type").removeClass('is-invalid').siblings('p')
                                        .removeClass('invalid-feedback').html("");
                                    }

                                    if (errors['discount_amount']) {

                                        $("#discount_amount").addClass('is-invalid').siblings('p')
                                        .addClass('invalid-feedback').html(errors['discount_amount']);
                                    } else{

                                        $("#discount_amount").removeClass('is-invalid').siblings('p')
                                        .removeClass('invalid-feedback').html("");
                                        }

                                    if (errors['status']) {

                                        $("#status").addClass('is-invalid').siblings('p')
                                        .addClass('invalid-feedback').html(errors['status']);
                                    } else{

                                        $("#status").removeClass('is-invalid').siblings('p')
                                        .removeClass('invalid-feedback').html("");
                                    }

                                    if (errors['starts_at']) {

                                        $("#starts_at").addClass('is-invalid').siblings('p')
                                        .addClass('invalid-feedback').html(errors['starts_at']);
                                    } else{

                                        $("#starts_at").removeClass('is-invalid').siblings('p')
                                        .removeClass('invalid-feedback').html("");
                                    }

                                    if (errors['expires_at']) {

                                        $("#expires_at").addClass('is-invalid').siblings('p')
                                        .addClass('invalid-feedback').html(errors['expires_at']);
                                    } else{

                                        $("#expires_at").removeClass('is-invalid').siblings('p')
                                        .removeClass('invalid-feedback').html("");
                                    }

                                }
                                    
                                    }, error: function(jqXHR, exception){
                                       console.log("Something Went Wrong");
                                 }
                                    
                            });
                        });


                        $("#name").change(function(){
                            element = $(this);

                            $("button[type=submit]").prop('disabled',true);
                            $.ajax({
                            url: '{{ route("getSlug") }}',
                            type: 'get',
                            data: {title: element.val()},
                            dataType: 'json',
                            success: function(response){

                                $("button[type=submit]").prop('disabled',false);
                                if(response["status"] == true){
                                    $("#slug").val(response["slug"]);
                                }
                            }
                        });
                    });
    
                       
                    </script>
                    <script>
                    
                    //dropzone means fileupload
                    Dropzone.autoDiscover = false;    
                    const dropzone = $("#image").dropzone({ 
                        init: function() {
                            this.on('addedfile', function(file) {
                                if (this.files.length > 1) {
                                    this.removeFile(this.files[0]);
            }
        });
    },
                    url:  "{{ route('temp-images.create') }}",
                    maxFiles: 1,
                    paramName: 'image',
                    addRemoveLinks: true,
                    acceptedFiles: "image/jpeg,image/png,image/gif",
                    headers: {
                               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                             }, success: function(file, response){
                               $("#image_id").val(response.image_id);
        
    }
});
                    </script>
                @endsection