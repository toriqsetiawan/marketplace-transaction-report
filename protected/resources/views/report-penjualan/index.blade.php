@extends('layouts.app')

@section('contentheader_title')
	Report Penjualan
@endsection

@section('htmlheader_title')
	Report Penjualan
@endsection

@section('main-content')
	<div class="row">
		<div class="col-lg-4 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-orange">
				<div class="inner">
					<h3>Shopee</h3>
					<h4>Rp. 1.000.000</h4>
				</div>
				<div class="icon">
					<i class="ion ion-social-usd"></i>
				</div>
				<a href="{{ url('report-penjualan/1?mode=shopee') }}" class="small-box-footer">
					More info <i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
        <div class="col-lg-4 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-green">
				<div class="inner">
					<h3>Tokopedia</h3>
					<h4>Rp. 1.000.000</h4>
				</div>
				<div class="icon">
					<i class="ion ion-social-bitcoin"></i>
				</div>
				<a href="{{ url('report-penjualan/1?mode=tokopedia') }}" class="small-box-footer">
					More info <i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
        <div class="col-lg-4 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-purple">
				<div class="inner">
					<h3>Lazada</h3>
					<h4>Rp. 1.000.000</h4>
				</div>
				<div class="icon">
					<i class="ion ion-social-euro"></i>
				</div>
				<a href="{{ url('report-penjualan/1?mode=lazada') }}" class="small-box-footer">
					More info <i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
        <div class="col-lg-4 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-gray">
				<div class="inner">
					<h3>Tiktok</h3>
					<h4>Rp. 1.000.000</h4>
				</div>
				<div class="icon">
					<i class="ion ion-social-yen"></i>
				</div>
				<a href="{{ url('report-penjualan/1?mode=tiktok') }}" class="small-box-footer">
					More info <i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
        <div class="col-lg-4 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-gray">
				<div class="inner">
					<h3>Mitra</h3>
					<h4>Rp. 1.000.000</h4>
				</div>
				<div class="icon">
					<i class="ion ion-social-whatsapp"></i>
				</div>
				<a href="{{ url('report-penjualan/1?mode=mitra') }}" class="small-box-footer">
					More info <i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
        <div class="col-lg-4 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-gray">
				<div class="inner">
					<h3>Offline</h3>
					<h4>Rp. 1.000.000</h4>
				</div>
				<div class="icon">
					<i class="ion ion-person-stalker"></i>
				</div>
				<a href="{{ url('report-penjualan/1?mode=offline') }}" class="small-box-footer">
					More info <i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
	</div>
@endsection
