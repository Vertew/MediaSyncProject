<div class = "mt-3 mb-3">

    <h3 class='display-6 text-center'>{{$title ?? "Nothing selected yet..."}}</h3>

    <video id="video_player" width="1280" height="720" controls >
        <source src=null type="video/mp4">
        Your browser does not support mp4 videos.
    </video>

    <audio id="audio_player" controls>
        <source src=null type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    
    <div class = "container-md mb-3 text-center">
        <h3 class='display-6 text-center'>Your videos</h3>
    </div>
    
    <button class="btn btn-light" type="button" onclick="setVidSrc({{ Js::from($current_file) }})">Set video</button>

    <div class = "container-md mb-3 text-center" style="max-height: 300px; overflow-y: auto;">
        @foreach ($videos as $video)
            <div class="container-md mt-3">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary">
                        <input type="radio" name="options" autocomplete="off" wire:click="set_media({{$video}})"/>
                        Select {{$video->title}}
                    </label>
                </div>
            </div>
        @endforeach
    </div>

    <div class = "container-md mb-3 text-center">
        <h3 class='display-6 text-center'>Your audio files</h3>
    </div>

    <button class="btn btn-light" type="button" onclick="setSoundSrc({{ Js::from($current_file) }})">Set audio</button>

    <div class = "container-md mb-3 text-center" style="max-height: 300px; overflow-y: auto;">
        @foreach ($audios as $audio)
            <div class="container-md mt-3">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary">
                        <input type="radio" name="options" autocomplete="off" wire:click="set_media({{$audio}})"/>
                        Select {{$audio->title}}
                    </label>
                </div>
            </div>
        @endforeach
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