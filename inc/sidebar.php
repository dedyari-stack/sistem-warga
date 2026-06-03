<?php if (!isset($activePage)) { $activePage = ''; } ?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-users-cog"></i>
        <span>GUYUB RUKUN</span>
    </div>
    
    <?php
    $isDataMasterActive = in_array($activePage, ['data-warga', 'data-aset', 'jenis-pemasukan', 'jenis-pengeluaran']);
    $isFinanceActive = in_array($activePage, ['pemasukan', 'kas-lain', 'pengeluaran']);
    $isAssetActive = in_array($activePage, ['sewa-aset', 'kondisi-aset']);
    $isReportActive = in_array($activePage, ['laporan-keuangan', 'laporan-rekap-keuangan', 'laporan-tunggakan', 'laporan-aset', 'laporan-sewa-aset']);
    ?>
    <nav class="sidebar-menu">
        <ul>
            <li class="menu-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item has-submenu <?= $isDataMasterActive ? 'open' : '' ?>">
                <div class="submenu-header" onclick="toggleSubmenu(this)">
                    <div>
                        <i class="fas fa-boxes"></i>
                        <span>Data Master</span>
                    </div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu-list">
                    <li><a href="data-warga.php" class="<?= $activePage === 'data-warga' ? 'active' : '' ?>">Data Warga</a></li>
                    <li><a href="data-aset.php" class="<?= $activePage === 'data-aset' ? 'active' : '' ?>">Data Aset</a></li>
                    <li><a href="jenis-pemasukan.php" class="<?= $activePage === 'jenis-pemasukan' ? 'active' : '' ?>">Jenis Pemasukan</a></li>
                    <li><a href="jenis-pengeluaran.php" class="<?= $activePage === 'jenis-pengeluaran' ? 'active' : '' ?>">Jenis Pengeluaran</a></li>
                </ul>
            </li>
            <li class="menu-item has-submenu <?= $isFinanceActive ? 'open' : '' ?>">
                <div class="submenu-header" onclick="toggleSubmenu(this)">
                    <div>
                        <i class="fas fa-wallet"></i>
                        <span>Keuangan</span>
                    </div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu-list">
                    <li><a href="pemasukan.php" class="<?= $activePage === 'pemasukan' ? 'active' : '' ?>">Pemasukan</a></li>
                    <li><a href="pengeluaran.php" class="<?= $activePage === 'pengeluaran' ? 'active' : '' ?>">Pengeluaran</a></li>
                </ul>
            </li>
            <li class="menu-item has-submenu <?= $isAssetActive ? 'open' : '' ?>">
                <div class="submenu-header" onclick="toggleSubmenu(this)">
                    <div>
                        <i class="fas fa-boxes"></i>
                        <span>Aset</span>
                    </div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu-list">
                    <li><a href="sewa-aset.php" class="<?= $activePage === 'sewa-aset' ? 'active' : '' ?>">Sewa Aset</a></li>
                    <li><a href="kondisi-aset.php" class="<?= $activePage === 'kondisi-aset' ? 'active' : '' ?>">Kondisi Aset</a></li>
                </ul>
            </li> 
            <li class="menu-item has-submenu <?= $isReportActive ? 'open' : '' ?>">
                <div class="submenu-header" onclick="toggleSubmenu(this)">
                    <div>
                        <i class="fas fa-boxes"></i>
                        <span>Laporan</span>
                    </div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu-list">
                    <li><a href="laporan-keuangan.php" class="<?= $activePage === 'laporan-keuangan' ? 'active' : '' ?>">Laporan Keuangan</a></li>
                    <li><a href="laporan-rekap-keuangan.php" class="<?= $activePage === 'laporan-rekap-keuangan' ? 'active' : '' ?>">Laporan Rekap Keuangan</a></li>
                    <li><a href="laporan-tunggakan.php" class="<?= $activePage === 'laporan-tunggakan' ? 'active' : '' ?>">Laporan Piutang</a></li>
                    <li><a href="laporan-aset.php" class="<?= $activePage === 'laporan-aset' ? 'active' : '' ?>">Laporan Aset</a></li>
                    <li><a href="laporan-sewa-aset.php" class="<?= $activePage === 'laporan-sewa-aset' ? 'active' : '' ?>">Laporan Sewa Aset</a></li>
                </ul>
            </li> 
            <li class="menu-item <?= $activePage === 'hak-akses' ? 'active' : '' ?>">
                <a href="hak-akses.php">
                    <i class="fas fa-user-shield"></i>
                    <span>Hak Akses</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-profile">
            <i class="fas fa-user-circle"></i>
            <div class="user-info">
                <span class="user-name">Admin RT</span>
                <span class="user-role">Pengurus RT/RW</span>
            </div>
        </div>
    </div>
</aside>
