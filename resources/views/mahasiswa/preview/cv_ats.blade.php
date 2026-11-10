<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>CV ATS - {{ $user->nama }}</title>
    <style>
        @page {
            margin: 2cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #111111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11.5pt;
            line-height: 1.45;
        }

        h1,
        h2,
        p,
        ul {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 2px solid #111111;
            text-align: center;
        }

        .header h1 {
            margin-bottom: 6px;
            font-size: 20pt;
            line-height: 1.2;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .contact {
            font-size: 10.5pt;
            color: #333333;
        }

        .section {
            margin-top: 18px;
        }

        .section-title {
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #555555;
            font-size: 12.5pt;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .entry {
            margin-bottom: 12px;
        }

        .entry:last-child {
            margin-bottom: 0;
        }

        .entry-title {
            font-size: 12pt;
            font-weight: bold;
        }

        .entry-subtitle {
            font-size: 11pt;
            margin-top: 1px;
        }

        .entry-meta {
            margin-top: 1px;
            font-size: 10.5pt;
            font-style: italic;
            color: #444444;
        }

        .entry-body {
            margin-top: 5px;
        }

        .entry-body ul {
            margin: 3px 0 0 18px;
        }

        .entry-body li {
            margin-bottom: 2px;
        }

        .bio {
            text-align: justify;
        }

        .skills {
            color: #111111;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $user->nama }}</h1>

        @php
            $kontak = array_filter([
                $user->email,
                $user->profil?->no_hp,
                $user->profil?->alamat,
            ]);
        @endphp

        @if ($kontak !== [])
            <p class="contact">{{ implode(' | ', $kontak) }}</p>
        @endif
    </div>

    @if (filled($user->profil?->bio))
        <section class="section">
            <h2 class="section-title">Tentang Saya</h2>
            <p class="bio">{{ $user->profil->bio }}</p>
        </section>
    @endif

    @if ($user->pendidikan->isNotEmpty())
        <section class="section">
            <h2 class="section-title">Pendidikan</h2>

            @foreach ($user->pendidikan as $pendidikan)
                <div class="entry">
                    <p class="entry-title">{{ $pendidikan->nama_institusi }}</p>

                    <p class="entry-subtitle">
                        {{ $pendidikan->jenjang }}@if (filled($pendidikan->jurusan)) - {{ $pendidikan->jurusan }}@endif
                    </p>

                    <p class="entry-meta">
                        {{ $pendidikan->tahun_mulai }} - {{ $pendidikan->tahun_selesai ?: 'Sekarang' }}@if (filled($pendidikan->nilai_akhir)) | IPK: {{ $pendidikan->nilai_akhir }}@endif
                    </p>
                </div>
            @endforeach
        </section>
    @endif

    @if ($user->pengalaman->isNotEmpty())
        <section class="section">
            <h2 class="section-title">Pengalaman</h2>

            @foreach ($user->pengalaman as $pengalaman)
                <div class="entry">
                    <p class="entry-title">{{ $pengalaman->posisi }}</p>
                    <p class="entry-subtitle">{{ $pengalaman->nama_institusi }}</p>

                    <p class="entry-meta">
                        {{ $pengalaman->jenis }} | {{ $pengalaman->tanggal_mulai?->format('M Y') }} - {{ $pengalaman->tanggal_selesai?->format('M Y') ?: 'Sekarang' }}
                    </p>

                    @if (filled($pengalaman->deskripsi))
                        <div class="entry-body">
                            <ul>
                                @foreach (preg_split('/\r\n|\r|\n/', $pengalaman->deskripsi) as $baris)
                                    @if (trim($baris) !== '')
                                        <li>{{ trim($baris) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    @if ($user->sertifikasi->isNotEmpty())
        <section class="section">
            <h2 class="section-title">Sertifikasi</h2>

            @foreach ($user->sertifikasi as $sertifikasi)
                <div class="entry">
                    <p class="entry-title">{{ $sertifikasi->nama_sertifikasi }}</p>

                    <p class="entry-meta">
                        {{ $sertifikasi->penerbit }}@if ($sertifikasi->tanggal_terbit) | {{ $sertifikasi->tanggal_terbit->format('Y') }}@endif
                    </p>
                </div>
            @endforeach
        </section>
    @endif

    @if ($user->keahlian->isNotEmpty())
        <section class="section">
            <h2 class="section-title">Keahlian</h2>

            <p class="skills">
                {{ $user->keahlian->map(fn ($keahlian) => $keahlian->nama_keahlian.' ('.$keahlian->tingkat.')')->implode(', ') }}
            </p>
        </section>
    @endif
</body>
</html>
