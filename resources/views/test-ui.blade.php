@extends('layouts.app')

@section('title', 'Test UI - '.config('app.name', 'SIMAKAR'))

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                Sanity Check Frontend
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                DataTables, Tom Select, Flatpickr, dan SweetAlert2.
            </p>
        </div>

        <section class="rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">DataTables</h2>

            <table id="myTable" class="min-w-full divide-y divide-gray-700 border border-gray-700 text-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ahmad Fauzi</td>
                        <td>Teknik Informatika</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Siti Aminah</td>
                        <td>Sistem Informasi</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Muhammad Rizki</td>
                        <td>Teknik Komputer</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Tom Select</h2>

                <select id="mySelect" placeholder="Pilih program studi">
                    <option value="">Pilih program studi</option>
                    <option value="ti">Teknik Informatika</option>
                    <option value="si">Sistem Informasi</option>
                    <option value="tk">Teknik Komputer</option>
                </select>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Flatpickr</h2>

                <input id="myDate" type="text" placeholder="Pilih tanggal" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20">
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">SweetAlert2</h2>

            <button
                id="testAlert"
                type="button"
                class="rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/30 active:bg-indigo-700"
            >
                Test Alert
            </button>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            $('#myTable').DataTable();
            new window.TomSelect('#mySelect');
            window.flatpickr('#myDate');

            document.getElementById('testAlert').addEventListener('click', () => {
                window.Swal.fire({
                    title: 'Berhasil!',
                    text: 'SweetAlert2 aktif di SIMAKAR!',
                    icon: 'success',
                });
            });
        });
    </script>
@endpush
