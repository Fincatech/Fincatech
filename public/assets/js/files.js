let FilesComponent = {
 
    inputs: [],
  
    init: function() {
      // Obtener todos los elementos input de tipo file con la clase file-attach
      const fileInputs = document.querySelectorAll('input[type="file"].file-attach');
  
      // Iterar sobre los elementos input
      fileInputs.forEach(input => {
        // Agregar el evento change a cada elemento input
        input.addEventListener('change', this.handleFileChange.bind(this));
        // Almacenar el elemento input en la lista
        this.inputs.push(input);
      });
    },
  
    handleFileChange: function(event) {
      const input = event.target;
      const files = input.files;
  
      // Verificar si se seleccionaron archivos
      if (files.length > 0) {
        // Leer cada archivo seleccionado y almacenarlos en base64
        const filePromises = Array.from(files).map(file => this.readFile(file));
        Promise.all(filePromises)
          .then(base64Files => {
            // Aquí puedes hacer algo con los ficheros en base64
            console.log('Archivos en base64:', base64Files);
          })
          .catch(error => {
            console.error('Error al leer los archivos:', error);
          });
      }
    },
  
    readFile: function(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
  
        // Evento onload que se dispara cuando la lectura del archivo se completa
        reader.onload = () => {
          const base64Data = reader.result;
          resolve(base64Data);
        };
  
        // Evento onerror que se dispara si hay un error durante la lectura del archivo
        reader.onerror = () => {
          reject(new Error('Error al leer el archivo'));
        };
  
        // Leer el archivo como base64
        reader.readAsDataURL(file);
      });
    }
  }
  
  
$(()=>{
    FilesComponent.init();
});
