<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        @if (! Auth::guest())
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{asset('/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->name }}</p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('adminlte_lang::message.online') }}</a>
                </div>
            </div>
        @endif

        <!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="{{ trans('adminlte_lang::message.search') }}..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">{{ trans('adminlte_lang::message.header') }}</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="{{ url('home') == request()->url() ? 'active':'' }}"><a href="{{ url('home') }}"><i class='fa fa-home'></i> <span>Home</span></a></li>
            <li class="{{ activateWhenRoute('taxonomi*')  }}"><a href="{{ route('taxonomi.index') }}"><i class='fa fa-th'></i> <span>Manajemen Satuan</span></a></li>
            <li class="{{ activateWhenRoute('varian*')  }}"><a href="{{ route('varian.index') }}"><i class='fa fa-truck'></i> <span>Manajemen Barang</span></a></li>
            <li class="{{ activateWhenRoute('employee*')  }}"><a href="{{ route('employee.index') }}"><i class='fa fa-users'></i> <span>Manajemen Pegawai</span></a></li>
            <li class="{{ activateWhenRoute('transaction*')  }}"><a href="{{ route('transaction.index') }}"><i class='fa fa-book'></i> <span>Manajemen Transaksi</span></a></li>
            <li class="{{ activateWhenRoute('print*')  }}"><a href="{{ route('print.index') }}"><i class='fa fa-print'></i> <span>Cetak Laporan</span></a></li>
            <hr>
            <li class="{{ activateWhenRoute('supplier*')  }}"><a href="{{ route('supplier.index') }}"><i class='fa fa-industry'></i> <span>Manajemen Supplier</span></a></li>
            <li class="{{ activateWhenRoute('mitra*')  }}"><a href="{{ route('mitra.index') }}"><i class='fa fa-users'></i> <span>Manajemen Mitra</span></a></li>
            <li class="{{ activateWhenRoute('product*')  }}"><a href="{{ route('product.index') }}"><i class='fa fa-folder'></i> <span>Manajemen Produk</span></a></li>
            <li class="{{ activateWhenRoute('config-fee*')  }}"><a href="{{ route('config-fee.index') }}"><i class='fa fa-dollar'></i> <span>Config fee</span></a></li>
            <li class="{{ activateWhenRoute('penjualan*')  }}"><a href="{{ route('penjualan.index') }}"><i class='fa fa-money'></i> <span>Penjualan</span></a></li>
            <li class="{{ activateWhenRoute('report-penjualan*')  }}"><a href="{{ route('report-penjualan.index') }}"><i class='fa fa-print'></i> <span>Report Penjualan</span></a></li>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
