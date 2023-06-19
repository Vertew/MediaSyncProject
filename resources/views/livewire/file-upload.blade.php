<div class = "container-md mt-3 text-center">
    <form wire:submit.prevent="save">
        @csrf
        <div class="container-md mt-3">
            <input type="file" class="form-control" wire:model="input" >
        </div>
        @error("input") <span class="error">{{ $message }}</span> @enderror
        <div class="container-md mt-3">
            <button type="submit" onclick="reset('upload-div')" class="btn btn-success">Upload</button>
            <button type="reset" onclick="showhide('upload-div')" class="btn btn-secondary">Close</button>
        </div>
    </form>
</div>
