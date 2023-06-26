<div class = "container-fluid mt-3 mb-3">

    {{-- Video and audio player bit --}}
    <div class = "row">
        <div class = "col-md-9">
            <div class = "container-md mt-5 text-center" >
                <h2 class='text-center'>{{$title_vid}}</h2>
            </div>

            <div class = "container-md text-center" id = "video-div">
                <video id="video_player" title={{$title_vid}} width="1280" height="720" controls >
                    <source src=null type="video/mp4">
                    Your browser does not support the selected media format.
                </video>
            </div>
        </div>

        <div class = "col-md-3">
            {{-- Chat message bit --}}
            <div class = "container-md mt-5 text-center" >
                <h2 class='text-center'>Chat</h2>
            </div>

            <div class = "container-md" wire:ignore>
                <div id="message-container" class = "container-md mt-3" style="min-height: 300px; max-height: 660px; overflow-y: auto;">
                    <ul class="list-group" id ="message-list">
                        {{-- Bit of a hacky dumb way to get the list items to start drawing from the bottom of the container --}}
                        <li style="min-height: 660px">
 
                    </ul>
                </div>

                <div class = "container-md mt-3 text-center">
                    <form id='form1'>
                        <div class="container-md mb-3 mt-3">
                            <input id="input" type = "text" class="form-control" placeholder="Start typing..." name = "title">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class = "container-md mt-5 text-center">
        <h2 class='text-center'>{{$title_snd}}</h2>
    </div>

    <div class = "container-md mt-3 text-center" id = "audio-div">
        <audio id="audio_player" title={{$title_snd}} controls>
            <source src=null type="audio/mpeg">
            Your browser does not support the selected media format.
        </audio>
    </div>

    
    {{-- Video and audio selection bit --}}
    <div class="row">
        <div class="col">
            <div class = "container-md mt-5 text-center">
                <h1 class='display-6 text-center'>Your video files</h1>
                <h4 class='text-center'>Select a video</h4>
            </div>
            
            <div class = "container-md mt-3 text-center" style="max-height: 300px; overflow-y: auto;">
                @foreach ($videos as $video)
                    <div class="container-md mt-3">
                        <div class="btn-group" role="group" id={{"btngrp".$video->id}} aria-label="Basic radio toggle button group">
                            <input type="radio" class='btn-check' name='btnradio' autocomplete="off" id={{"vidbutton".$video->id}} wire:click="set_media({{$video}})"/>    
                            <label class="btn btn-outline-primary" for={{"vidbutton".$video->id}}>{{$video->title}}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class = "container-md mt-3 text-center">
                <button class="btn btn-light" type="button" onclick="setSrc('video_player',{{ Js::from($current_file) }},{{ Js::from($slctd_title_vid) }},'video')">Add to video player</button>
                <button class="btn btn-outline-danger" type="button" wire:click="delete({{ $slctd_id_vid ?? -1 }})">Delete</button>
            </div>

        </div>

        <div class="col">
            <div class = "container-md mt-5 text-center">
                <h1 class='display-6 text-center'>Your audio files</h1>
                <h4 class='text-center'>Select audio</h4>
            </div>

            <div class = "container-md mt-3 text-center" style="max-height: 300px; overflow-y: auto;">
                @foreach ($audios as $audio)
                    <div class="container-md mt-3">
                        <div class="btn-group" role="group" id="btngrp2" aria-label="Basic radio toggle button group">
                            <input type="radio" class='btn-check' name='btnradio' autocomplete="off" id={{"sndbutton".$audio->id}} wire:click="set_media({{$audio}})"/>    
                            <label class="btn btn-outline-primary" for={{"sndbutton".$audio->id}}>{{$audio->title}}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class = "container-md mt-3 text-center">
                <button class="btn btn-light" type="button" onclick="setSrc('audio_player',{{ Js::from($current_file) }},{{ Js::from($slctd_title_snd) }},'audio')">Add to audio player</button>
                <button class="btn btn-outline-danger" type="button" wire:click="delete({{ $slctd_id_snd ?? -1 }})">Delete</button>
            </div>
        </div>
    </div>

    <script> 
        function setSrc(id,newSrc,title,type) {
            var myElement = document.getElementById(id);
            myElement.src=newSrc;
            @this.set_title(title,type);
        }
    </script>
</div>