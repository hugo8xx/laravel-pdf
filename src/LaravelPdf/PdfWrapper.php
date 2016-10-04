<?php

namespace niklasravnsborg\LaravelPdf;

/**
 * Laravel PDF: mPDF wrapper for Laravel 5
 *
 * @package laravel-pdf
 * @author Niklas Ravnsborg-Gjertsen
 */
class PdfWrapper {

	protected $mpdf;
	protected $rendered = false;
	protected $options;

	public function __construct($mpdf) {
		$this->mpdf = $mpdf;
		$this->options = array();
	}

	/**
	 * Load a HTML string
	 *
	 * @param string $string
	 * @return static
	 */
	public function loadHTML($string, $mode = 0) {
		$this->mpdf->WriteHTML((string) $string, $mode);
		$this->html = null;
		$this->file = null;
		return $this;
	}

	/**
	 * Load a HTML file
	 *
	 * @param string $file
	 * @return static
	 */
	public function loadFile($file) {
		$this->html = null;
		$this->file = $file;
		return $this;
	}

	/**
	 * Load a View and convert to HTML
	 *
	 * @param string $view
	 * @param array $data
	 * @param array $mergeData
	 * @return static
	 */
	public function loadView($view, $data = array(), $mergeData = array()) {
		$this->html = \View::make($view, $data, $mergeData)->render();
		$this->file = null;
		return $this;
	}

	/**
	 * Output the PDF as a string.
	 *
	 * @return string The rendered PDF as string
	 */
	public function output() {

		if($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output('', 'S');
	}

	/**
	 * Save the PDF to a file
	 *
	 * @param $filename
	 * @return static
	 */
	public function save($filename) {
		$this->mpdf = new \mPDF(
			Config::get('pdf.mode'),              // mode - default ''
			Config::get('pdf.format'),            // format - A4, for example, default ''
			Config::get('pdf.default_font_size'), // font size - default 0
			Config::get('pdf.default_font'),      // default font family
			Config::get('pdf.margin_left'),       // margin_left
			Config::get('pdf.margin_right'),      // margin right
			Config::get('pdf.margin_top'),        // margin top
			Config::get('pdf.margin_bottom'),     // margin bottom
			Config::get('pdf.margin_header'),     // margin header
			Config::get('pdf.margin_footer'),     // margin footer
			Config::get('pdf.orientation')        // L - landscape, P - portrait
		);

		$this->mpdf->SetTitle(Config::get('pdf.title'));
		$this->mpdf->SetAuthor(Config::get('pdf.author'));
		$this->mpdf->SetWatermarkText(Config::get('pdf.watermark'));
		$this->mpdf->SetDisplayMode(Config::get('pdf.display_mode'));
		$this->mpdf->showWatermarkText = Config::get('pdf.show_watermark');
		$this->mpdf->watermark_font = Config::get('pdf.watermark_font');
		$this->mpdf->watermarkTextAlpha = Config::get('pdf.watermark_text_alpha');


		if($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output($filename, 'F');
	}

	/**
	 * Make the PDF downloadable by the user
	 *
	 * @param string $filename
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function download($filename = 'document.pdf') {

		if ($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif ($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output($filename, 'D');
	}

	/**
	 * Return a response with the PDF to show in the browser
	 *
	 * @param string $filename
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function stream($filename = 'document.pdf' ){
		if ($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output($filename, 'I');
	}

	public function __call($name, $arguments){
		return call_user_func_array(array($this->mpdf, $name), $arguments);
	}

}
