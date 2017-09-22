@extends('layouts.master')

@section('page-title', 'Students')

@section('meta')
	<meta name="csrf-token" content="{{csrf_token()}}">
@endsection

@section('page-css')
	<!-- Animate css -->
	<link href="{{ asset("/bower_components/AdminLTE/plugins/animate/animate.min.css") }}" rel="stylesheet" type="text/css" />
	<!-- swal alert css -->
	<link href="{{ asset("/bower_components/AdminLTE/plugins/sweetalert-master/dist/sweetalert.css") }}" rel="stylesheet" type="text/css" />
	<!-- datatables -->
	<link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css") }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-header', 'Students')

@section('user-logout')
  @component('components.user-logout')
      @slot('user_name')
          {{Auth::guard('web')-> user()->user_name}}
      @endslot
      {{route('user.logout')}}
  @endcomponent
@endsection


@section('sidebar-navigation')
<!-- Sidebar Menu -->
<ul class="sidebar-menu">
  <li class="header">USER NAVIGATION</li>

  <li class="">
    <a href="{{route('user.dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span>
    </a>
  </li>

  <!-- guardians -->
  <li class="treeview">
    <a href="#"><i class="fa fa-user"></i> <span>Guardians</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    </a>
    <ul class="treeview-menu">
      <li><a href="/users/guardians"><i class="glyphicon glyphicon-th-list"></i> <span>Guardians</span></a></li>
      <li><a href="/users/guardians/create"><i class="fa fa-pencil"></i>New Guardian</a></li>
    </ul>
  </li>

  <!-- student -->
  <li class="treeview active">
    <a href="#">
      <i class="fa fa-users"></i><span>Students</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    </a>
    <ul class="treeview-menu">
      <li class="active"><a href="/users/students"><i class="glyphicon glyphicon-list-alt"></i>Student List</a></li>
      <li><a href="/users/students/create"><i class="fa fa-pencil"></i>Student Admission</a></li>
    </ul>
  </li>

  <!-- score -->
  <li>
    <a href="/users/scores"><i class="glyphicon glyphicon-list-alt"></i> Score Tables
    </a>
  </li>
</ul>
@endsection


@section('content')
	
	@can('create-student')
		<div class="row">
			<div class="col-md-12">
				<a href="/users/students/create" class="btn btn-primary btn-sm btn-flat pull-right">New Student</a>
			</div>
		</div> <br>
	@endcan

	<div class="row">
		<div class="col-md-12">

			<div class="panel panel-default">
				<!-- Default panel contents -->
				<div class="panel-heading">
					Students
				</div>
				<div class="panel-body">
					<table class="table table-responsive table-striped table-condensed table-bordered" id="student-table">
						<thead>
							<tr>
								<th>Name</th>
								<th>Birth Date</th>
								<th>Address</th>
								<th>County</th>
								<th>Country</th>
								<th>Grade</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach($students as $student)
								<tr>

									<td>{{$student->first_name}} {{$student->surname}}</td>
									<td>{{$student->date_of_birth->toFormattedDateString()}}</td>
									<td>{{$student->address}}</td>
									<td>{{$student->county}}</td>
									<td>{{$student->country}}</td>
									<td>{{$student->grade->name}}</td>

									<td>
										@can('show-student')
											<a id="edit-student" href="/users/students/edit/{{$student->id}}" data-toggle="tooltip" title="Edit" href="#" role="button">
												<i class="glyphicon glyphicon-edit text-info"></i>
											</a> &nbsp;
										@endcan

										@can('delete-student')
											<a id="delete-student" data-id="{{$student->id}}" data-toggle="tooltip" title="Delete" href="#" role="button">
												<i class="glyphicon glyphicon-trash text-danger"></i>
											</a>
										@endcan
									</td>

								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<!-- /. close of panel div -->
		</div>
	</div>

@endsection


@section('page-scripts')

	<script src="{{ asset ("/bower_components/AdminLTE/plugins/sweetalert-master/dist/sweetalert.min.js") }}"></script>
	<script src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}"></script>
	<script src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}"></script>

	<script type="text/javascript">

		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$('#student-table').DataTable();

		// deleting a student
		$(document).on('click', '#delete-student', function(event) {
			event.preventDefault();
			/* Act on the event */

			// id of the row to be deleted
			var id = $(this).attr('data-id');

		    // row to be deleted
		    var row = $(this).parent("td").parent("tr");

			var message = "You'll not be able to retrieve this student!";

			var route = "/students/delete/"+id;

			var item = "student";


			swal_delete(message, item, route, row);
		});	
	</script>

	@if($flash = session('message'))
        <script type="text/javascript">
            var message = "Student <b>{{$flash}}</b> updated!";
            notify(message);
        </script>
    @endif


@endsection