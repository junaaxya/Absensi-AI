<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- HEADER -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Manajemen Karyawan</h2>
                        <p class="text-gray-600">Kelola Data Karyawan dan Hak Akses Sistem</p>
                    </div>
                </div>

                <!-- FILTER & ACTION BAR -->
                <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between items-center bg-gray-100 p-4 rounded-lg">
                    <!-- Search -->
                    <div class="relative w-full md:w-1/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" placeholder="Cari" class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-10">
                    </div>

                    <!-- Filters (Mockup for now) -->
                    <div class="flex gap-4 w-full md:w-auto">
                        <select class="rounded-md border-gray-300 shadow-sm h-10 w-full md:w-40 text-sm">
                            <option>Jabatan: Semua</option>
                        </select>
                        <select class="rounded-md border-gray-300 shadow-sm h-10 w-full md:w-40 text-sm">
                            <option>Role: Semua</option>
                        </select>
                    </div>

                    <!-- Add Button -->
                    <button onclick="openModal('addEmployeeModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center h-10 w-full md:w-auto justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Karyawan</span>
                    </button>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">NIP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Jabatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status Wajah</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($employees as $index => $employee)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $employee->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">22222222</td> <!-- Placeholder NIP -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee->jabatan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($employee->role) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($employee->has_face_data)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ Terdaftar
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        X Belum
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button onclick="showDetail({{ $employee }})" class="text-indigo-600 hover:text-indigo-900 bg-white border border-gray-300 rounded px-3 py-1 text-xs mr-2">
                                        ⓘ Detail
                                    </button>
                                    <button class="text-gray-600 hover:text-gray-900 bg-white border border-gray-300 rounded px-3 py-1 text-xs">
                                        👤 Kelola Wajah
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                 <div class="mt-4">
                    {{ $employees->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH KARYAWAN -->
    <div id="addEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('addEmployeeModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Content -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-2 border-black">
                <div class="bg-gray-200 px-4 py-3 border-b border-black flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tambah Karyawan</h3>
                    <button onclick="closeModal('addEmployeeModal')" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                </div>
                
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="space-y-4">
                            <!-- Helper Text -->
                            <p class="text-sm text-gray-500">Isi Data Identitas dan Akun Karyawan</p>
                            <hr>

                            <!-- Nama Lengkap -->
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label for="name" class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="col-span-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100" required>
                            </div>

                            <!-- NIP (Placeholder as username for now or custom field) -->
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label for="username" class="text-sm font-medium text-gray-700">Username/NIP</label>
                                <input type="text" name="username" id="username" class="col-span-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100" required>
                            </div>

                             <!-- Email -->
                             <div class="grid grid-cols-3 gap-4 items-center">
                                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" class="col-span-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100" required>
                            </div>
                            
                            <!-- Jabatan -->
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label for="jabatan" class="text-sm font-medium text-gray-700">Jabatan</label>
                                <select name="jabatan" id="jabatan" class="col-span-2 mt-1 block w-full py-2 px-3 border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="Karyawan">Karyawan</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Manager">Manager</option>
                                </select>
                            </div>

                             <!-- Role -->
                             <div class="grid grid-cols-3 gap-4 items-center">
                                <label for="role" class="text-sm font-medium text-gray-700">Role</label>
                                <select name="role" id="role" class="col-span-2 mt-1 block w-full py-2 px-3 border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="karyawan">Karyawan</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <!-- Password -->
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" id="password" class="col-span-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100" required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="grid grid-cols-3 gap-4 items-center">
                                <label for="password_confirmation" class="text-sm font-medium text-gray-700">Konfirmasi</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="col-span-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100" required>
                            </div>
                            
                            <!-- Status Wajah Info -->
                            <div class="bg-gray-200 p-2 text-xs flex items-center gap-2 mt-4">
                                <span class="font-bold text-red-600">X Belum Terdaftar</span>
                                <span class="text-gray-500">Daftar Wajah Dapat di Daftarkan setelah karyawan di simpan</span>
                            </div>

                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-300 hover:bg-gray-400 text-base font-medium text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm border border-black">
                            💾 Simpan
                        </button>
                        <button type="button" onclick="closeModal('addEmployeeModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm border border-black">
                             ✖ Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL KARYAWAN -->
    <div id="detailEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('detailEmployeeModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border-2 border-black">
                <div class="bg-gray-200 px-4 py-3 border-b border-black flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Karyawan</h3>
                        <p class="text-sm text-gray-600">Informasi dan Pengaturan Akun Karyawan</p>
                    </div>
                    <button onclick="closeModal('detailEmployeeModal')" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                </div>
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Identitas Karyawan -->
                    <div class="border border-black p-4 bg-gray-50 mb-4">
                         <h4 class="font-bold border-b border-black mb-2 pb-1">Identitas Karyawan</h4>
                         <div class="grid grid-cols-3 gap-2 text-sm">
                             <div class="font-semibold">Nama Lengkap</div>
                             <div class="col-span-2">: <span id="detail-name">-</span></div>
                             
                             <div class="font-semibold">NIP / Karyawan</div>
                             <div class="col-span-2">: <span id="detail-nip">-</span></div>

                             <div class="font-semibold">Email</div>
                             <div class="col-span-2">: <span id="detail-email">-</span></div>

                             <div class="font-semibold">Tanggal Bergabung</div>
                             <div class="col-span-2">: <span id="detail-joined">-</span></div>

                             <div class="font-semibold">Role Sistem</div>
                             <div class="col-span-2 flex items-center gap-2">
                                 : 
                                 <select id="detail-role" class="py-0 px-2 border-gray-300 text-sm rounded bg-white">
                                     <option value="karyawan">Karyawan</option>
                                     <option value="admin">Admin</option>
                                 </select>
                             </div>

                             <div class="font-semibold">Jabatan</div>
                             <div class="col-span-2 flex items-center gap-2">
                                 : 
                                 <select id="detail-jabatan" class="py-0 px-2 border-gray-300 text-sm rounded bg-white">
                                     <option value="Karyawan">Karyawan</option>
                                     <option value="Staff">Staff</option>
                                     <option value="Manager">Manager</option>
                                 </select>
                                 <button class="ml-auto bg-gray-200 border border-gray-400 px-2 py-1 text-xs rounded hover:bg-gray-300">
                                     ✏️ Reset Password
                                 </button>
                             </div>
                         </div>
                    </div>

                    <!-- Lower Section: Account & Face Data -->
                     <div class="grid grid-cols-2 gap-4">
                         <!-- Data Akun Login -->
                         <div class="border border-black p-3 bg-gray-50 text-sm">
                             <h4 class="font-bold border-b border-black mb-2 pb-1">Data Akun Login</h4>
                             <div class="grid grid-cols-[80px_1fr] gap-1">
                                 <div>Username</div>
                                 <div>: <span id="detail-username">-</span></div>
                                 <div class="mt-1">
                                     <button class="bg-gray-200 border border-gray-400 px-2 py-1 text-xs rounded hover:bg-gray-300 w-full text-center">
                                         ⬅ Kembali Ke Manajemen Karyawan
                                     </button>
                                 </div>
                             </div>
                         </div>

                         <!-- Status & Actions -->
                          <div class="space-y-2">
                              <!-- Status Wajah -->
                              <div class="border border-black p-2 bg-gray-50 flex items-center justify-between">
                                  <div class="text-sm font-bold">Status Data Wajah</div>
                                  <div id="detail-face-status" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded border border-green-300">
                                      ✓ Terdaftar
                                  </div>
                              </div>

                              <!-- Action Buttons -->
                              <button onclick="openFaceManagement()" class="w-full bg-gray-300 hover:bg-gray-400 text-black font-medium py-1 px-2 text-sm border border-black rounded shadow-sm">
                                  Kelola Data Wajah
                              </button>
                              <button class="w-full bg-gray-300 hover:bg-gray-400 text-black font-medium py-1 px-2 text-sm border border-black rounded shadow-sm flex items-center justify-center gap-1">
                                  💾 Simpan Perubahan
                              </button>
                              <button class="w-full bg-gray-300 hover:bg-gray-400 text-black font-medium py-1 px-2 text-sm border border-black rounded shadow-sm flex items-center justify-center gap-1">
                                  🗑️ Hapus Data Karyawan
                              </button>
                          </div>
                     </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KELOLA DATA WAJAH -->
    <div id="faceHeaderModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('faceHeaderModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border-2 border-black">
                <!-- Header -->
                <div class="bg-gray-200 px-4 py-3 border-b border-black flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Kelola Data Wajah Karyawan</h3>
                        <p class="text-sm text-gray-600">Pendaftaran dan Pengelolaan Data Wajah Untuk Absensi</p>
                    </div>
                    <button onclick="closeModal('faceHeaderModal')" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                </div>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Identity Box -->
                    <div class="border border-black p-3 bg-gray-50 mb-4 text-sm">
                        <h4 class="font-bold border-b border-black mb-2 pb-1">Identitas Karyawan</h4>
                        <div class="grid grid-cols-[100px_1fr] gap-4">
                             <!-- Photo Placeholder -->
                             <div class="w-24 h-24 bg-gray-200 border border-gray-400 flex items-center justify-center">
                                 <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                 </svg>
                             </div>
                             
                             <!-- Details -->
                             <div class="grid grid-cols-[120px_1fr] gap-1">
                                 <div>Nama Lengkap</div> <div>: <span id="face-name">Andi</span></div>
                                 <div>NIP / Karyawan</div> <div>: <span id="face-nip">11111111</span></div>
                                 <div>Email</div> <div>: <span id="face-email">Andi@example.com</span></div>
                                 <div>Role Sistem</div> <div>: <span id="face-role">Karyawan</span></div>
                                 <div>Jabatan</div> <div>: <span id="face-jabatan">Staff</span></div>
                             </div>
                        </div>
                    </div>

                    <!-- Main Content Grid -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <!-- Left: Status Info -->
                         <div class="border border-black p-2 flex flex-col justify-between">
                             <div>
                                 <div class="font-bold border-b border-gray-300 mb-2">Status Data Wajah</div>
                                 <div class="text-sm">
                                     <span class="text-green-600 font-bold">✓ Terdaftar</span><br>
                                     Jumlah Foto : <span id="face-count">0</span>
                                 </div>
                             </div>
                         </div>

                         <!-- Right: Upload Box -->
                         <div class="border border-black bg-gray-100 flex flex-col items-center justify-center p-6 text-center cursor-pointer hover:bg-gray-200 transition" onclick="document.getElementById('face-upload').click()">
                             <svg class="w-12 h-12 text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                             </svg>
                             <span class="font-bold text-gray-800">Pilih Foto Wajah</span>
                             <span class="text-xs text-gray-500 mt-1">Unggah 6 Foto Wajah Dengan Posisi Sesuai Instruksi Admin</span>
                             <input type="file" id="face-upload" class="hidden" multiple accept="image/*" onchange="handleFiles(this.files)">
                         </div>
                     </div>

                     <!-- Dataset Table -->
                      <div class="mt-4 border border-black p-2">
                          <div class="flex justify-between items-center mb-2">
                              <h4 class="font-bold">Dataset Wajah Tersimpan</h4>
                              <button class="text-xs bg-gray-200 border border-gray-400 px-2 py-1 hover:bg-gray-300">
                                  🗑️ Hapus Dataset Wajah
                              </button>
                          </div>
                          
                          <table class="w-full border-collapse border border-gray-300 text-sm">
                              <thead class="bg-gray-200">
                                  <tr>
                                      <th class="border border-gray-300 p-1">No</th>
                                      <th class="border border-gray-300 p-1">Foto</th>
                                      <th class="border border-gray-300 p-1">Status</th>
                                      <th class="border border-gray-300 p-1">Aksi</th>
                                  </tr>
                              </thead>
                              <tbody id="face-table-body">
                                  <!-- Rows will be populated by JS -->
                              </tbody>
                          </table>
                      </div>

                      <!-- Footer Action -->
                       <div class="mt-4 text-center">
                           <button onclick="uploadFaces()" class="bg-gray-200 border-2 border-black px-6 py-2 font-bold hover:bg-gray-300 flex items-center justify-center mx-auto gap-2">
                               <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                               </svg>
                               Proses & Simpan Data Wajah
                           </button>
                       </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        let currentEmployeeId = null;
        let selectedFiles = [];

        function showDetail(employee) {
            // Populate Identitas
            document.getElementById('detail-name').innerText = employee.name;
            document.getElementById('detail-nip').innerText = '22222222'; // Placeholder
            document.getElementById('detail-email').innerText = employee.email;
            document.getElementById('detail-joined').innerText = new Date(employee.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            
            // Set Selects
            document.getElementById('detail-role').value = employee.role;
            document.getElementById('detail-jabatan').value = employee.jabatan || 'Karyawan';

            // Populate Account
            document.getElementById('detail-username').innerText = employee.username;

            // Update Face Status in Modal
            const statusBox = document.querySelector('#detailEmployeeModal .text-xs.bg-green-100'); // Selector might need improvement
            // Better approach: Assign ID to the status element in modal first
            const statusDiv = document.getElementById('detail-face-status');
            if(statusDiv) {
                if(employee.has_face_data) {
                    statusDiv.className = "text-xs bg-green-100 text-green-800 px-2 py-1 rounded border border-green-300";
                    statusDiv.innerText = "✓ Terdaftar";
                } else {
                    statusDiv.className = "text-xs bg-red-100 text-red-800 px-2 py-1 rounded border border-red-300";
                    statusDiv.innerText = "X Belum Terdaftar";
                }
            }

            // Trigger for Face Management
            // Store employee object globally or pass ID
            currentEmployeeId = employee.id; 
            
            // Show Modal
            openModal('detailEmployeeModal');
        }

        function openFaceManagement() {
            closeModal('detailEmployeeModal');
            // Populate Face Modal Info (Simplified for now, reuse existing data if possible or fetch fresh)
            document.getElementById('face-name').innerText = document.getElementById('detail-name').innerText;
            openModal('faceHeaderModal');
        }

        function handleFiles(files) {
            const tbody = document.getElementById('face-table-body');
            tbody.innerHTML = ''; // Clear previous
            selectedFiles = Array.from(files).slice(0, 6); // Limit to 6

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const row = `
                        <tr>
                            <td class="border border-gray-300 p-1 text-center">${index + 1}</td>
                            <td class="border border-gray-300 p-1 text-center">
                                <img src="${e.target.result}" class="h-10 w-10 object-cover mx-auto border border-gray-400">
                            </td>
                            <td class="border border-gray-300 p-1 text-center text-xs">
                                <span class="bg-gray-200 px-1 rounded">Pending</span>
                            </td>
                            <td class="border border-gray-300 p-1 text-center">
                                <button class="text-red-600 hover:text-red-800">🗑️</button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                };
                reader.readAsDataURL(file);
            });
            
            document.getElementById('face-count').innerText = selectedFiles.length;
        }
        async function uploadFaces() {
            if (selectedFiles.length < 1) {
                alert('Pilih minimal 1 foto wajah!');
                return;
            }

            const formData = new FormData();
            formData.append('user_id', currentEmployeeId);
            selectedFiles.forEach((file) => {
                formData.append('photos[]', file);
            });

            // Show loading state (Optional: add spinner)
            const submitBtn = document.querySelector('#faceHeaderModal button:last-child');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Memproses...';

            try {
                const response = await fetch('/api/face/register', { // Endpoint to be created
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Gagal mengupload data wajah');

                const result = await response.json();
                alert('Data wajah berhasil disimpan!');
                closeModal('faceHeaderModal');
                location.reload(); // Reload to update status
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
</x-app-layout>
