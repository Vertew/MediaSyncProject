<div class = "mt-3 mb-3">

    <h1 class='display-6 text-center'>{{$title_vid}}</h1>

    <div class = "container-md text-center" id = "video-div">
        <video id="video_player" width="1280" height="720" controls >
            <source src=null type="video/mp4">
            Your browser does not support the selected media format.
        </video>
    </div>

    <div class = "container-md mt-5 text-center">
        <h1 class='display-6 text-center'>{{$title_snd}}</h1>
    </div>

    <div class = "container-md mt-3 text-center" id = "audio-div">
        <audio id="audio_player" controls>
            <source src=null type="audio/mpeg">
            Your browser does not support the selected media format.
        </audio>
    </div>
    
    <div class="row">

        <div class="col">
            <div class = "container-md mt-5 text-center">
                <h1 class='display-6 text-center'>Your video files</h1>
                <h4 class='text-center'>Select a video to play</h4>
            </div>
            
            <div class = "container-md mt-3 text-center" style="max-height: 300px; overflow-y: auto;">
                @foreach ($videos as $video)
                    <div class="container-md mt-3">
                        <div class="btn-group" role="group" id="btngrp1" aria-label="Basic radio toggle button group">
                            <input type="radio" class='btn-check' name='btnradio' autocomplete="off" id={{"vidbutton".$video->id}} wire:click="set_media({{$video}})"/>    
                            <label class="btn btn-outline-primary" for={{"vidbutton".$video->id}}>{{$video->title}}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class = "container-md mt-3 text-center">
                <button class="btn btn-light" type="button" onclick="setVidSrc({{ Js::from($current_file) }})">Add to video player</button>
            </div>

        </div>

        <div class="col">
            <div class = "container-md mt-5 text-center">
                <h1 class='display-6 text-center'>Your audio files</h1>
                <h4 class='text-center'>Select some audio to play</h4>
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
                <button class="btn btn-light" type="button" onclick="setSoundSrc({{ Js::from($current_file) }})">Add to audio player</button>
            </div>

        </div>

    </div>

    <script> 
        var myVideo = document.getElementById("video_player");
        var myAudio = document.getElementById("audio_player");
        function setVidSrc(newSrc) {
            myVideo.src=newSrc
        }
        function setSoundSrc(newSrc) {
            myAudio.src=newSrc
        }
    </script>
    

</div>