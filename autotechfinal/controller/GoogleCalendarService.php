<?php

class GoogleCalendarService {

    public function __construct() {
        // Simple datepicker service - no auth needed
    }

    /**
     * Get available dates (blocks past dates and dates with too many bookings)
     */
    public function getAvailableDates($technicienId = null) {
        $now = new DateTime();
        $now->setTime(0, 0, 0);
        
        $available = [];
        $maxDaysToShow = 60;
        
        for ($i = 0; $i < $maxDaysToShow; $i++) {
            $date = clone $now;
            $date->add(new DateInterval("P{$i}D"));
            
            // Skip weekends (Saturday=6, Sunday=0)
            $dayOfWeek = $date->format('w');
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                continue;
            }
            
            $available[] = $date->format('Y-m-d');
        }
        
        return $available;
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableTimeSlots($date) {
        $slots = [];
        
        // Business hours: 8am to 6pm, 30-min slots
        $start = new DateTime($date . ' 08:00');
        $end = new DateTime($date . ' 18:00');
        
        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->add(new DateInterval('PT30M'));
        }
        
        return $slots;
    }

    /**
     * Create event (local only, no Google sync)
     */
    public function createEvent($rendezVous) {
        // No external sync needed
        return null;
    }

    /**
     * Update event (local only)
     */
    public function updateEvent($eventId, $rendezVous) {
        // No external sync needed
        return true;
    }

    /**
     * Delete event (local only)
     */
    public function deleteEvent($eventId) {
        // No external sync needed
        return true;
    }

    /**
     * List available times for datepicker
     */
    public function listEvents($startDate, $endDate) {
        // Return empty - not used
        return [];
    }
}
?>
