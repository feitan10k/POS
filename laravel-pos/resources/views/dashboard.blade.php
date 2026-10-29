@extends('layout')
@section('content')
<div class="page-heading"><div><h1>ภาพรวมร้าน</h1><p>สรุปการดำเนินงานประจำวัน</p></div><a class="btn" href="/pos">เริ่มขายสินค้า</a></div>
<div class="grid">
<div class="card metric"><div class="metric-label">ยอดขายวันนี้</div><div class="metric-value">{{ number_format($sales) }}</div><div class="metric-note">รายการ</div></div>
<div class="card metric"><div class="metric-label">รายรับวันนี้</div><div class="metric-value">{{ number_format($revenue,2) }}</div><div class="metric-note">บาท</div></div>
<div class="card metric"><div class="metric-label">สินค้าในระบบ</div><div class="metric-value">{{ number_format($products) }}</div><div class="metric-note">รายการ</div></div>
<div class="card metric"><div class="metric-label">สมาชิก</div><div class="metric-value">{{ number_format($customers) }}</div><div class="metric-note">คน</div></div></div>
<div class="card"><div class="toolbar"><div><h2>สินค้าใกล้หมด</h2><div class="text-muted">สินค้าเหลือไม่เกิน 5 ชิ้น</div></div><a href="/products">จัดการสินค้า</a></div>@forelse($low as $p)<p>{{ $p->name }} <span class="badge warning">เหลือ {{ $p->stock }} ชิ้น</span></p>@empty<div class="empty-state">ไม่มีสินค้าใกล้หมด</div>@endforelse</div>
<div class="card"><div class="toolbar"><h2>รายการขายล่าสุด</h2><a href="/sales">ดูประวัติการขาย</a></div><div class="table-wrap"><table><thead><tr><th>เลขที่ใบเสร็จ</th><th>ลูกค้า</th><th class="text-right">ยอดขาย</th><th>วันและเวลา</th></tr></thead><tbody>@forelse($recent as $s)<tr><td><a href="/sales/{{ $s->id }}">{{ $s->receipt_no }}</a></td><td>{{ $s->customer_name }}</td><td class="text-right">{{ number_format($s->total,2) }} บาท</td><td>{{ $s->created_at }}</td></tr>@empty<tr><td colspan="4" class="empty-state">ยังไม่มีรายการขาย</td></tr>@endforelse</tbody></table></div></div>
@endsection
