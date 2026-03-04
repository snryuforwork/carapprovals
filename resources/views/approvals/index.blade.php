@extends('layout')

@section('title', 'รายการใบอนุมัติ')

@section('content')

@php
    $user = auth()->user();
    $isSale = $user && $user->role === 'sale';
    $isAdmin = $user && $user->role === 'admin';
@endphp

{{-- แถว 1: ปุ่มสร้าง --}}
    @if(auth()->user()->role == 'sale')
        <div class="d-flex justify-content-start mb-3">
            <a href="{{ route('approvals.create') }}" class="btn btn-success">
                {{ __('messages.newpaper') }}
            </a>
        </div>
    @endif

{{-- แถว 2: ตัวกรอง + เรียงวันที่ --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form action="{{ route('approvals.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
        <select name="sales_user_id" class="form-select form-select-sm" style="width:160px" onchange="this.form.submit()">
            <option value="">{{ __('messages.sales_user_id') }}</option>
            @foreach($salesList as $id => $name)
                <option value="{{ $id }}" {{ request('sales_user_id') == $id ? 'selected' : '' }}>
                    {{ $name }}</option>
            @endforeach
        </select>

        <select name="status"
                class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()">
            <option value="">{{ __('messages.statusSort') }}</option>    
            @foreach ($statusList as $st)    
            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                {{ $st }}</option>
            @endforeach
        </select>

        <input type="hidden" name="sort" value="{{ request('sort','newest') }}">
    </form>

    <div class="d-flex align-items-center gap-2">
        @php
            $currentSort = request('sort','newest');
            $toggleSort = $currentSort === 'newest' ? 'oldest' : 'newest';
            $toggleText = $currentSort === 'newest' ? __('messages.sortN') : __('messages.sortO');
        @endphp
        <a href="{{ route('approvals.index', ['sort' => $toggleSort, 'sales_user_id' => request('sales_user_id'), 'status' => request('status')]) }}" class="btn btn-sm btn-outline-primary">
            {{ $toggleText }}
        </a>
    </div>
</div>

{{-- แถว 3: ตารางหลัก --}}
<div class="table-responsive">
    <table class="table table-bordered table-sm align-middle">
        <thead class="text-center bg-light">
            <tr>
                <th>#</th>
                <th>{{ __('messages.GroupID') }}</th>
                <th>{{ __('messages.car_model') }}</th>
                <th>{{ __('messages.sales_name') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.updated_at') }}</th>
                <th>{{ __('messages.action') }}</th>
            </tr>
        </thead>
        <tbody>
        @forelse($mainApprovals as $approval)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $approval->group_id }}</td>
                <td class="text-center">{{ $approval->car_model }}</td> 
                <td class="text-center">{{ $approval->sales_name }}</td>
                <td class="text-center">
                    {{-- แสดงสถานะตามจริง --}}
                    @if($approval->status == 'Waiting')
                        <span class="badge px-3 py-2" style="background-color: #ff833b; color: black;">{{ __('messages.statusW') }}</span>
                    @elseif($approval->status == 'Approved')
                        <span class="badge px-3 py-2" style="background-color: #03b11a; color: white;">{{ __('messages.statusA') }}</span>
                    @elseif($approval->status == 'Reject')
                        <span class="badge px-3 py-2" style="background-color: #fe1c1c; color: white;">{{ __('messages.statusR') }}</span>
                    @endif
                </td>
                <td class="text-center text-muted small">{{ $approval->updated_at }}</td>
                <td class="text-center">
                    @php $role = strtolower($user->role); @endphp
                    @if($role == 'admin')
                        @include('approvals.partials.actions_admin', ['approval' => $approval])
                    @elseif($role == 'sale')
                        @include('approvals.partials.actions_sale', ['approval' => $approval])
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted p-4">{{ __('messages.notitext') }}</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- ตาราง Draft  --}}
@if(($isSale || $isAdmin) && $draftApprovals->count() > 0)
    <hr class="my-5">
    <h6 class="fw-bold mb-3 text-secondary">
        <i class="fas fa-exclamation-circle"></i> 
        {{ $isAdmin ? 'งานที่ต้องแก้ไข (Reject)' :__('messages.text') }}
    </h6>   
        
    
        <thead class="text-center bg-light">
        <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">            
            <thead class="table-light text-center">
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.GroupID') }}</th>
                    <th>{{ __('messages.car_model') }}</th>
                    <th>{{ __('messages.sales_name') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.action') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($draftApprovals as $approval)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $approval->group_id }}</td>
                    <td class="text-center">{{ $approval->car_model }}</td>
                    <td class="text-center">{{ $approval->sales_name }}</td>
                    <td class="text-center">
                        @if($approval->status == 'Reject')
                            <span class="badge px-3 py-2" style="background-color: #fe1c1c; color: white;">{{ __('messages.statusR') }}</span>
                        @else
                            <span class="badge px-3 py-2" style="background-color: #f3d30a; color: black;">{{ __('messages.statusD') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('approvals.show', $approval->group_id) }}" 
                                class="btn btn-sm btn-secondary" style="opacity: 0.6;">
                                {{ __('messages.details') }}
                            </a>
                            {{-- 1. ปุ่มแก้ไข (มีทั้ง Admin และ Sale) --}}
                            <a href="{{ route('approvals.edit', $approval->id) }}" class="btn btn-warning btn-sm" title="แก้ไข">
                                <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                            </a>

                            {{-- 2. ปุ่มลบ (เฉพาะ Sale เท่านั้น!!) --}}
                            @if($isSale)
                                <form action="{{ route('approvals.destroy', $approval->group_id) }}" 
                                    method="POST" onsubmit="return confirm('ยืนยันที่จะลบข้อมูลนี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="ลบ">
                                            <i class="bi bi-trash"></i> 
                                        </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection