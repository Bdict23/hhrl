@extends('layouts.master')
@section('content')


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row">
        <!-- Main Content -->
        <div class="w-full lg:w-3/4 mb-4 lg:mb-0 lg:mr-4">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-4">
                <!-- Create Post Section -->
                <h3 class="font-semibold text-lg mb-3">Create a Post</h3>
                <div class="flex flex-col sm:flex-row mb-3">
                    <div class="flex sm:flex-row sm:space-x-3 w-full">
                        <div class=" w-10 h-10 rounded-full bg-gray-500 text-white flex items-center justify-center font-bold mb-3 sm:mb-0">
                            {{ strtoupper(substr('Manager', 0, 1)) }}
                        </div>
                        <div class="ml-1 mb-2 w-full flex-1 border rounded-full  px-4 pt-3 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400">
                            <span class="">What's on your mind, Benedict?</span>
                        </div>
                    </div>

                </div>
                <div class="flex justify-end text-sm text-gray-500 border-t pt-2">
                    <button class="flex items-center space-x-1 hover:text-blue-500 mr-2">
                        <img src="https://static.xx.fbcdn.net/rsrc.php/v4/yd/r/pkbalDbTOVI.png?_nc_eui2=AeHGIDbcv2QOFfD_b5j1JowTxUvOOua_OjbFS8465r86NolKLYlb1ZjXPLCAmPPEQ7GXMEombhCzVQGGJ6TTvyBN" alt="Live Video" class="w-5 h-5">
                        <span>Survey</span>
                    </button>
                    <button class="flex items-center space-x-1 hover:text-blue-500">
                        <img src="https://static.xx.fbcdn.net/rsrc.php/v4/y7/r/Ivw7nhRtXyo.png" alt="Photo/Video" class="w-5 h-5">
                        <span>Photo/Video</span>
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-4">
                <!-- Post Header -->
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-gray-500 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr('Manager', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold">Manager</p>
                        <p class="text-xs text-gray-500">April 29, 2025 • 🌍 Public</p>
                    </div>
                </div>
              
                <!-- Post Content -->
                <p class="mb-3 text-gray-700">announcement of the day! 🌞🍹</p>
              
                <!-- Post Image -->
                <img src="https://via.placeholder.com/500x300" alt="Post Image" class="rounded-lg mb-3">
              
                <!-- Reactions -->
                <div class="flex justify-between text-sm text-gray-500 border-t pt-2">
                    <div>
                        <span>💬 30 Comments</span>
                    </div>
                </div>
                  
                <!-- Comment Box (hidden by default) -->
                <div class="comment-box mt-3">
                    <div class="flex items-center space-x-2">
                        <input type="text" placeholder="Write a comment..."
                            class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        <button class="bg-blue-500 text-white rounded-full px-4 py-2 text-sm">Post</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
       <div class="row w-full lg:w-1/4 lg:ml-4">
         <div class="w-full lg:w-1/4 lg:ml-4 bg-white rounded-2xl shadow-sm p-2 mb-3">
             <div class=" pt-2"> 
                 <h3 class="font-semibold text-lg mb-3">Summary</h3>
                 <div class="border-t">
                 </div>
             </div>
         </div>
         <div class="w-full lg:w-1/4 lg:ml-4 bg-white rounded-2xl shadow-sm p-2">
             <div class="border-t pt-2"> 
                 <h3 class="font-semibold text-lg mb-3">Online Users</h3>
                 <ul class="space-y-2">
                     {{-- @foreach ($users ?: [] as $user)
                         <li class="flex items-center space-x-3">
                             <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold">
                                 {{ strtoupper(substr($user->name, 0, 1)) }}
                             </div>
                             <span>{{ $user->name }}</span>
                             @if ($user->isOnline() ?? false)
                                 <span class="text-green-500 text-xs">Online</span>
                             @else
                                 <span class="text-gray-500 text-xs">Offline</span>
                             @endif
                         </li>
                     @endforeach --}}
                 </ul>
                 <div class="border-t flex items-center space-x-3">
                     <div class="w-10 h-10 rounded-full bg-gray-500 text-white flex items-center justify-center font-bold">
                         {{ strtoupper(substr('You', 0, 1)) }}
                     </div>
                     <div class="m-4">
                        <span>Benedict</span>
                        @if (auth()->user())
                            <span class="text-green-500 text-xs">Online</span>
                        @else
                            <span class="text-gray-500 text-xs">Offline</span>
                        @endif
                     </div>
                 </div>
                 <div>
                 </div>
             </div>
         </div>
       </div>


    </div>
</div>
    
@endsection
