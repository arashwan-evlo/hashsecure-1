<section class="no-print hidden">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action('\Modules\Essentials\Http\Controllers\DashboardController@hrmDashboard')}}"><i class="fa fas fa-users"></i> {{__('essentials::lang.hrm')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @if (auth()->user()->can('add_essentials_leave_type'))
                        <li @if(request()->segment(2) == 'leave-type') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController@index')}}">@lang('essentials::lang.leave_type')</a></li>
                    @endif
                    <li @if(request()->segment(2) == 'leave') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsLeaveController@index')}}">@lang('essentials::lang.leave')</a></li>

                    <li @if(request()->segment(2) == 'attendance') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\AttendanceController@index')}}">@lang('essentials::lang.attendance')</a></li>

                    <li @if(request()->segment(2) == 'allowance-deduction') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController@index')}}">@lang('essentials::lang.allowance_and_deduction')</a></li>

                    <li @if(request()->segment(2) == 'payroll') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\PayrollController@index')}}">@lang('essentials::lang.payroll')</a></li>

                    <li @if(request()->segment(2) == 'holiday') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@index')}}">@lang('essentials::lang.holiday')</a></li>

                    <li @if(request()->get('type') == 'hrm_department') class="active" @endif><a href="{{action('TaxonomyController@index') . '?type=hrm_department'}}">@lang('essentials::lang.departments')</a></li>

                    <li @if(request()->get('type') == 'hrm_designation') class="active" @endif><a href="{{action('TaxonomyController@index') . '?type=hrm_designation'}}">@lang('essentials::lang.designations')</a></li>

                    @if(auth()->user()->can('edit_essentials_settings'))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsSettingsController@edit')}}">@lang('business.settings')</a></li>
                    @endif
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>