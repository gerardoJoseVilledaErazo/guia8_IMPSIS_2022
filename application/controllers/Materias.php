<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Materias extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');//Visto en guia5
        if (!isset($this->session->userdata['logged_in'])) {
            redirect("/");
        }
    }

    public function index()
    {
        $this->load->model('Materia_model');
        $data = array(
            "records" => $this->Materia_model->getAll(),
            "title" => "Materias"
        );
        $this->load->view("shared/header", $data);
        $this->load->view("materias/index", $data);
        $this->load->view("shared/footer", $data);
    }

    public function insertar()
    {
        $this->load->model('Materia_model');
        $data = array(
            "materias" => $this->Materia_model->getAll(),
            "title" => "Insertar carrera"
        );
        $this->load->view("shared/header", $data);
        $this->load->view("materias/add_edit", $data);
        $this->load->view("shared/footer", $data);
    }

    public function modificar($id)
    {
        $this->load->model('Materia_model');
        $materia = $this->Materia_model->getById($id);
        $data = array(
            "materias" => $this->Materia_model->getAll(),
            "materia" => $materia,
            "title" => "Modificar materia",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("materias/add_edit", $data);
        $this->load->view("shared/footer", $data);
    }
    // FIN - FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////

    // FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////

    public function add()
    {
        // Reglas de validación del formulario
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como máximo una cantidad determinada de caracteres.
         */
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules(
            "idmateria",
            "Id Materia",
            "required|min_length[10]|max_length[10]|is_unique[materias.idmateria]"
        );
        $this->form_validation->set_rules(
            "materia",
            "Materia",
            "required|min_length[3]|max_length[50]"
        );

        // Modificando el mensaje de validación para los errores
        $this->form_validation->set_message(
            'required',
            'El campo %s es requerido.'
        );
        $this->form_validation->set_message(
            'min_length',
            'El campo %s debe tener al menos %s caracteres.'
        );
        $this->form_validation->set_message(
            'max_length',
            'El campo %s debe tener como máximo %s caracteres.'
        );
        $this->form_validation->set_message(
            'is_unique', 
            'El campo %s ya existe.'
        );
        $this->form_validation->set_message(
            'alpha', 
            'El campo %s debe contener solo caracteres alfabeticos.'
        );

        // Parámetros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validación del formulario
        if ($this->form_validation->run()) {
            // Si la validación fue exitosa, entonces entra acá
            try {
                $this->load->model('Materia_model');
                // Se crea el objeto con los campos de la tabla de materias
                $data = array(
                    "idmateria" => $this->input->post("idmateria"),
                    "materia" => $this->input->post("materia"),
                );
                // Se pasan los valores al método insert del modelo
                $rows = $this->Materia_model->insert($data);
                // Si $rows devuelve un valor mayor a 1, la inserción fue exitosa
                if ($rows > 0) {
                    $msg = "Información guardada correctamente.";
                } else {
                    // Si $rows entra acá es porque hubo un error al insertar
                    $statusCode = 500;
                    $msg = "No se pudo guardar la información.";
                }
            } catch (Exception $ex) {
                // Si entra acá es porque hubo un error al momento de ejecutar este método
                $statusCode = 500;
                $msg = "Ocurrió un error.";
            }
        } else {
            // Si hubo errores de validación entra acá
            $statusCode = 400;
            $msg = "Ocurrieron errores de validación.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        $this->data['msg'] = $msg;
        $this->output->set_status_header($statusCode);
        // Se devuelve el objeto json con la información
        echo json_encode($this->data);
    }

    public function update()
    {
        // Reglas de validación del formulario
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como máximo una cantidad determinada de caracteres.
         */
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules(
            "idmateria", 
            "Id Materia", 
            "required|min_length[10]|max_length[10]");
        $this->form_validation->set_rules(
            "materia", 
            "Materia", 
            "required|min_length[3]|max_length[50]");

        // Modificando el mensaje de validación para los errores, en este caso para
        // la regla required
        $this->form_validation->set_message(
            'required', 
            'El campo %s es requerido.'
        );
        $this->form_validation->set_message(
            'min_length', 
            'El campo %s debe tener al menos %s caracteres.'
        );
        $this->form_validation->set_message(
            'max_length', 
            'El campo %s debe tener como máximo %s caracteres.'
        );

        // Parámetros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validación del formulario
        if ($this->form_validation->run()) {
            // Si la validación fue exitosa, entonces entra acá
            try {
                $this->load->model('Materia_model');
                // Se crea el objeto con los campos de la tabla de materias
                $data = array(
                    "idmateria" => $this->input->post("idmateria"),
                    "materia" => $this->input->post("materia"),
                );
                // Se pasan los valores al método update del modelo, junto con la llave primaria
                $rows = $this->Materia_model->update($data, $this->input->post("PK_materia"));
                $msg = "Información guardada correctamente.";
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurrió un error.";
            }
        } else {
            $statusCode = 400;
            $msg = "Ocurrieron errores de validación.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        $this->data['msg'] = $msg;
        $this->output->set_status_header($statusCode);
        // Se devuelve el objeto json con la información
        echo json_encode($this->data);
    }

    public function eliminar($id)
    {
        $this->load->model('Materia_model');
        $result = $this->Materia_model->delete($id);
        if ($result) {
            $this->session->set_flashdata('success', "Registro borrado correctamente.");
        } else {
            $this->session->set_flashdata('error', "No se pudo borrar el registro.");
        }
        redirect("materias");
    }
    // FIN - FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////

    /* ----------------------------------------------------------------------------- */
    /*                          FUNCION PARA GENERAR REPORTE                         */
    /* ----------------------------------------------------------------------------- */

    public function report_todas_las_materias(){//
        //Se carga la libreria para generar tablas
        $this->load->library("table");
        //Se carga la libreria Report que acabamos de crear
        $this->load->library("Report");
        
        $pdf = new Report(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTFF-8', false);
        $pdf->titulo = "Listado de Materias";//
        //Informacion del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Gerardo Villeda');
        $pdf->SetTitle('Listado de Materias');//
        $pdf->SetSubject('Report generado usando Codeigniter y TCPDF');
        $pdf->SetKeywords('TCPDF, PDF, MySQL, Codeigniter');

        //Informacion por defecto del encabezado

        //Fuente de encabezado y pie de pagina
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        //Fuente por defecto Monospaced
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Margenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->setHeaderMargin(15);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

        //Quiebre de pagina automatico
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        //Factor de escala de imagen
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //Fuente del contenido
        $pdf->SetFont('Helvetica','',10);

        // --------------------------------------------------------------------

        //Generar la tabla y su informacion
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1">',
            'heading_cell_start' => '<th style="font-weight: bold; color:white;
            background-color: #13CDF7">',
        );

        $this->table->set_template($template);

        $this->table->set_heading('Id Materia','Materia');//

        $this->load->model('Materia_model');
        $materias = $this->Materia_model->getAll();//aqui

        foreach ($materias as $materia):
            $this->table->add_row($materia->idmateria, $materia->materia);
        endforeach;

        $html = $this->table->generate();
        //Generar la informacion de la tabla

        //Añadir pagina
        $pdf->AddPage();

        //Contenido de salida en HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        //Reiniciar puntero a la ultima pagina
        $pdf->lastPage();

        //cerrar y mostrar el reporte
        $pdf->Output(md5(time()) . '.pdf', 'I');    
    }
    /* ----------------------------------------------------------------------------- */
    /*                         /FUNCION PARA GENERAR REPORTE                         */
    /* ----------------------------------------------------------------------------- */

}
?>