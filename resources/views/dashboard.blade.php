@extends('layouts.master')
@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row">
        <!-- Main Content -->
        <div class="w-full lg:w-3/4 mb-4 lg:mb-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-4">
                <!-- Create Post Section -->
                <h3 class="font-semibold text-lg mb-3">Create a Post</h3>
                <div class="flex flex-col sm:flex-row items-center sm:space-x-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold mb-3 sm:mb-0">
                        {{ strtoupper(substr('Benedict', 0, 1)) }}
                    </div>
                    <input type="text" placeholder="What's on your mind, Benedict?"
                        class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                </div>
               
                <div class="flex justify-end text-sm text-gray-500 border-t pt-2">
                    <button class="flex items-center space-x-1 hover:text-blue-500 mr-2">
                        <img src="https://static.xx.fbcdn.net/rsrc.php/v4/yr/r/c0dWho49-X3.png" alt="Live Video" class="w-5 h-5">
                        <span>Survey</span>
                    </button>
                    <button class="flex items-center space-x-1 hover:text-blue-500">
                        <img src="https://static.xx.fbcdn.net/rsrc.php/v4/y7/r/Ivw7nhRtXyo.png" alt="Photo/Video" class="w-5 h-5">
                        <span>Photo/Video</span>
                    </button>
                </div>
            </div>

            <div class="max-w-xl mx-auto my-4 p-4 bg-white rounded-2xl shadow">
                <!-- Post Header -->
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
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
                        <span>30 Comments</span>
                    </div>
                </div>
              
                <!-- Action Buttons -->
                <div class="flex justify-between text-sm text-gray-600 border-t border-b py-2 mt-2">
                    <button onclick="toggleCommentBox(this)" class="flex items-center space-x-1 hover:text-blue-500">
                        💬 <span>Comment</span>
                    </button>
                </div>
              
                <!-- Comment Box (hidden by default) -->
                <div class="comment-box mt-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr('Manager', 0, 1)) }}
                        </div>
                        <input type="text" placeholder="Write a comment..."
                            class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        <button class="bg-blue-500 text-white rounded-full px-4 py-2">Post</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="w-full lg:w-1/4 lg:ml-4 bg-white rounded-2xl shadow p-4">
            <h3 class="font-semibold text-lg mb-3">Online Users</h3>
            <ul class="space-y-2">
                <li class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr('Alice', 0, 1)) }}
                    </div>
                    <span>Benedict</span>
                </li>
                <li class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr('Bob', 0, 1)) }}
                    </div>
                    <span>Keno</span>
                </li>
                <li class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr('Charlie', 0, 1)) }}
                    </div>
                    <span>Rald</span>
                </li>
            </ul>
        </div>
    </div>
</div>
    
@endsection
