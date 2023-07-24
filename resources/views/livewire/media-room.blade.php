<div class = "container-fluid mt-3 mb-3">
    <button class="btn btn-light" id="dump" type="button" wire:click="dump"><b>Dump</b></button>
    {{-- User list bit --}}
    <div class = "container-fluid text-center">
        <div class = "row">
            <div class = "col-md-4">
            </div>
            <div class = "col-md-4">
                <div class="container-sm">
                    <h2>Online Users</h2>
                    <ul class = "list-group" id="user-list" style="max-height: 100px; overflow-y: auto;">
                        @foreach($currentUsers as $user)
                            <li id="list-{{$user['username']}}">
                                <span class="list-group-item {{Auth::user()->username==$user['username'] ? "text-bg-primary" : "text-bg-light"}}">{{$user['username']}}
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal-{{$user['username']}}">{{App\Models\User::find($user['id'])->roles->firstWhere('pivot.room_id', $this->room->id)->role}}</button>
                                    @if($user['id'] != Auth::user()->id && Auth::user()->friends->doesntContain($user['id']))
                                        <button class="btn btn-success btn-sm" type="button" wire:click="sendRequest({{$user['id']}})">Add</button>
                                    @elseif($user['id'] != Auth::user()->id)
                                        <button class="btn btn-success btn-sm">Friend</button>
                                    @endif
                                    <button class="btn btn-danger btn-sm {{Gate::allows('moderator-action', $this->room->id) && App\Models\User::find($user['id'])->roles->firstWhere('pivot.room_id', $this->room->id)->role!='Admin'  ? "" : "disabled"}}" type="button" wire:click="kick({{$user['id']}})"><b>X</b></button>
                                </span>
                            </li>
                            {{-- Roles modal --}}
                            <div class="modal" id="modal-{{$user['username']}}" wire:ignore.self>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">{{$user["username"]}}</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                
                                        <div class="modal-body">
                                            <h5 class="modal-title">Current role</h5>
                                            <button class="btn btn-light" type="button"><b>{{App\Models\User::find($user['id'])->roles->where('pivot.room_id', $this->room->id)->first()->role}}</b></button> 
                                            <h5 class="modal-title">Change role</h5>
                                            @foreach($roles->where('role', '!=', App\Models\User::find($user['id'])->roles->where('pivot.room_id', $this->room->id)->first()->role) as $role)
                                                <button class="btn btn-light" type="button" wire:click="toggleRole({{$role->id}}, {{$user['id']}})"><b>{{$role->role}}</b></button> 
                                            @endforeach
                                        </div>
                                
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class = "col-md-4">
            </div>
        </div>
    </div>
    <div class = "row">
        {{-- File Queue bit --}}
        <div class = "col-md-2">
            <div class = "container-md mt-5 text-center" >
                <h2>File Queue</h2>
            </div>
            <div class="container-md text-center">
                <button class="btn btn-sm {{$queue_mode=="sequential"  ? "btn-secondary" : "btn-outline-secondary"}}" id="sequential-button" type="button" data-bs-toggle="tooltip" title="Sequential mode" wire:click="broadcastMode('sequential')"><b>&#129034;</b></button>
                <button class="btn btn-sm {{$queue_mode=="random"  ? "btn-secondary" : "btn-outline-secondary"}}" id="random-button" type="button" data-bs-toggle="tooltip" title="Random mode" wire:click="broadcastMode('random')"><b>Random</b></button>
                <button class="btn btn-sm {{$queue_mode=="vote"  ? "btn-secondary" : "btn-outline-secondary"}}" id="vote-button" type="button" data-bs-toggle="tooltip" title="Vote mode" wire:click="broadcastMode('vote')"><b>&#128587;</b></button>
            </div>
            <div class = "container-md mt-2 text-center">
                <button class="btn btn-primary btn-sm" id="play-queue" type="button" data-bs-toggle="tooltip" title="Play next in queue" wire:click="playNext"><b>&#x1F782;</b></button>
            </div>
            <div class = "container-md mt-3 text-center" id="file-container" style="min-height: 300px; max-height: 700px; overflow-y: auto;">
                @forelse ($queue as $file)
                    <div class="container-md mt-2 d-grid">
                        @if ($queue_mode == "vote")
                            <input type="radio" class='btn-check' name='btnradio' autocomplete="off" id={{"queuebutton".$file->id}} wire:click="placeVote({{$file}})"/>    
                            <label class="btn btn-outline-primary btn-block d-flex justify-content-between align-items-center" for={{"queuebutton".$file->id}}>{{$file->title}}
                                <span class="badge bg-success" data-bs-toggle="tooltip" title="Votes">{{($room->files->find($file->id))->pivot->votes}}</span>
                                <button class="btn btn-danger btn-sm" type="button" data-bs-toggle="tooltip" title="Remove from queue" wire:click="removeFromQueue({{ $file->id }})"><b>X</b></button>
                            </label>
                        @else
                            <input type="radio" class='btn-check' name='btnradio' autocomplete="off" id={{"queuebutton".$file->id}}/>    
                            <label class="btn btn-outline-primary btn-block d-flex justify-content-between align-items-center" for={{"queuebutton".$file->id}}>{{$file->title}}
                                <button class="btn btn-danger btn-sm" type="button" data-bs-toggle="tooltip" title="Remove from queue" wire:click="removeFromQueue({{ $file->id }})"><b>X</b></button>
                            </label>
                        @endif
                    </div>
                @empty
                    <p>Nothing in the queue right now...</p>
                @endforelse
            </div>
        </div>
        {{-- Media player bit --}}
        <div class = "col-md-8">
            <div class = "container-md mt-5 text-center" >
                <h2 class='text-center'>{{$title}}</h2>
            </div>
            <div class = "container-md text-center bg-dark" id = "media-div">
                <video id="media-player" class="w-100" preload="metadata" title={{$title}}>
                    <source src="source" type="video/mp4, audio/mpeg">
                    Your browser does not support the selected media format.
                </video>
                {{-- Defining custom media controls --}}
                <div class = "row" id="media-controls" wire:ignore>
                    <div class = "col-md-1">
                        <button class="btn btn-light" id="playpause" type="button">&#x1F782;</button>
                    </div>
                    <div class = "col-md-1" style = "margin-top: 8px">
                        <p class="text-light" id="time-text">00:00/00:00</p>
                    </div>
                    <div class = "col-md-8" style = "margin-top: 12px">
                        <div class="progress" id = "progress">
                            <div class="progress-bar" id="progress-bar" role="progressbar" style="transition: none" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class = "col-md-1">
                        <button class="btn btn-light" id="volume-toggle" value="0" type="button">&#128266;</button>
                        <input type="range" name="volume" id="volume-slider" class="form-range" min="0" max="1" step="0.05" value="1"/>
                    </div>
                    <div class = "col-md-1">
                        <button class="btn btn-light" id="fs" type="button"><b>&#x26F6;</b></button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Chat message bit --}}
        <div class = "col-md-2">
            <div class = "container-md mt-5 text-center" wire:ignore>
                <h2 class='text-center'>Chat</h2>
            </div>

            <div class = "container-md">
                <div id="message-container" class = "container-md mt-3" style="min-height: 300px; max-height: 680px; overflow-y: auto;" wire:ignore>
                    <ul class="list-group" id ="message-list">
                        {{-- Bit of a hacky dumb way to get the list items to start drawing from the bottom of the container --}}
                        <li style="min-height: 680px">
                    </ul>
                </div>

                <div class = "container-md mt-3 text-center">
                    <form id='form1'>
                        <div class="container-md">
                            @if($standard_level)
                                <input id="input" type = "text" class="form-control" placeholder="Start typing..." name = "title">
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class = "container-md mt-3 text-center" id="alert-container" style="max-height: 300px; overflow-y: auto;" wire:ignore>
        {{-- Alerts go here via js --}}
    </div>
    
    {{-- Video and audio selection bit --}}
    <div class="row">
        <div class="col">
            <div class = "container-md mt-5 text-center">
                <h1 class='display-6 text-center'>Your video files</h1>
                <h4 class='text-center'>Select a video</h4>
            </div>
            
            <div class = "container-md mt-3 text-center" style="max-height: 300px; overflow-y: auto;" wire:poll>
                <div class="btn-group-vertical" role="group" id="btngrp-video" aria-label="Basic radio toggle button group">
                    @foreach ($videos->reverse() as $video)
                        <div class="container-md mt-2">
                            <input type="radio" class='btn-check' name='btnradio' autocomplete="off" id={{"vidbutton".$video->id}} wire:click="set_media({{$video}})"/>    
                            <label class="btn btn-outline-primary" for={{"vidbutton".$video->id}}>{{$video->title}}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class = "container-md mt-3 text-center">
                {{--<button class="btn btn-light" type="button" onclick="setSrc('video_player',{{ Js::from($current_file) }},{{ Js::from($slctd_title_vid) }},'video')">Add to video player</button>--}}
                <button class="btn btn-success {{$video_slctd && $moderator_level  ? "" : "disabled"}}" type="button" id="add-video" onclick= "sendIdSet({{ Js::from($current_file) }})">Add to media player</button>
                <button class="btn btn-primary {{$video_slctd && $standard_level  ? "" : "disabled"}}" type="button" id="dlt-audio" onclick= "sendIdQueue({{ Js::from($current_file) }})">Add to queue</button>
                <button class="btn btn-danger {{$video_slctd  ? "" : "disabled"}}" type="button" id="dlt-video" wire:click="delete({{ $current_file ?? -1 }})">Delete</button>
            </div>

        </div>

        <div class="col">
            <div class = "container-md mt-5 text-center">
                <h1 class='display-6 text-center'>Your audio files</h1>
                <h4 class='text-center'>Select audio</h4>
            </div>

            <div class = "container-md mt-3 text-center" style="max-height: 300px; overflow-y: auto;">
                <div class="btn-group-vertical" role="group" id="btngrp-audio" aria-label="Basic radio toggle button group">
                    @foreach ($audios->reverse() as $audio)
                        <div class="container-md mt-2">
                            <input type="radio" class='btn-check' name='btnradio' autocomplete="off" id={{"sndbutton".$audio->id}} wire:click="set_media({{$audio}})"/>    
                            <label class="btn btn-outline-primary" for={{"sndbutton".$audio->id}}>{{$audio->title}}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class = "container-md mt-3 text-center">
                <button class="btn btn-success {{$audio_slctd && $moderator_level  ? "" : "disabled"}}" type="button" id="add-audio" onclick= "sendIdSet({{ Js::from($current_file) }})">Add to media player</button>
                <button class="btn btn-primary {{$audio_slctd && $standard_level  ? "" : "disabled"}}" type="button" id="dlt-audio" onclick= "sendIdQueue({{ Js::from($current_file) }})">Add to queue</button>
                <button class="btn btn-danger {{$audio_slctd  ? "" : "disabled"}}" type="button" id="dlt-audio" wire:click="delete({{ $current_file ?? -1 }})">Delete</button>
            </div>
        </div>
    </div>

    <script> 
        function setSrc(newSrc,title,type) {
            var myElement = document.getElementById("media-player");
            myElement.src=newSrc;
            @this.set_title(title,type);
        }
        function sendIdSet(new_id) {
            axios.post('/media-set', {
                file: new_id,
                room_id: currentRoom
            })
        }
        function sendIdQueue(new_id) {
            axios.post('/update-queue', {
                file: new_id,
                room_id: currentRoom
            })
        }
    </script>
</div>